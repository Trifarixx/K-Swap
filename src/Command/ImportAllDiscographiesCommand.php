<?php

namespace App\Command;

use App\Entity\Groupe;
use App\Entity\Discographie;
use App\Repository\ArtisteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-all-discographies',
    description: 'Importation massive avec protection anti-crash',
)]
class ImportAllDiscographiesCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ArtisteRepository $artisteRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $conn = $this->entityManager->getConnection(); // On récupère la connexion SQL brute
        
        $jsonFilePath = __DIR__ . '/../../all_discographies.json';
        if (!file_exists($jsonFilePath)) {
            $io->error("Fichier JSON introuvable.");
            return Command::FAILURE;
        }

        $discographiesData = json_decode(file_get_contents($jsonFilePath), true);
        $io->progressStart(count($discographiesData));

        foreach ($discographiesData as $data) {
            // 1. Nettoyage préventif des noms
            $nomArtiste = substr(preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $data['nomArtiste'] ?? 'Inconnu'), 0, 50);

            // 2. Gestion de l'Artiste via Doctrine (plus simple pour l'héritage)
            $artiste = $this->artisteRepository->findOneBy(['nomScene' => $nomArtiste]);
            if (!$artiste) {
                $artiste = new Groupe();
                $artiste->setNomScene($nomArtiste);
                $artiste->setNomGroupe($nomArtiste);
                if (!empty($data['genres'])) { $artiste->setGenres($data['genres']); }
                $this->entityManager->persist($artiste);
                $this->entityManager->flush(); 
            }

            foreach ($data['albums'] as $albumData) {
                $titreAlbum = substr(preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $albumData['titre'] ?? 'Album'), 0, 255);
                
                // 3. Insertion de la Discographie en SQL Brut pour éviter les blocages
                $conn->executeStatement("INSERT IGNORE INTO discographie (artiste_id, titre, type, pochette) VALUES (?, ?, ?, ?)", [
                    $artiste->getId(),
                    $titreAlbum,
                    substr($albumData['type'] ?? 'Album', 0, 20),
                    $albumData['pochette'] ?? null
                ]);

                // On récupère l'ID de l'album qu'on vient d'insérer (ou l'existant)
                $albumId = $conn->fetchOne("SELECT id FROM discographie WHERE titre = ? AND artiste_id = ?", [$titreAlbum, $artiste->getId()]);

                if ($albumId && !empty($albumData['morceaux'])) {
                    foreach ($albumData['morceaux'] as $morceauData) {
                        $titreM = substr(preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $morceauData['titre'] ?? 'Piste'), 0, 255);
                        
                        // 4. L'INSERTION MAGIQUE : INSERT IGNORE
                        // Si le titre contient un caractère qui déplaît à MySQL, 
                        // IGNORE fera que MySQL ignore la ligne au lieu de faire planter PHP !
                        try {
                            $conn->executeStatement("INSERT IGNORE INTO morceau (discographie_id, titre) VALUES (?, ?)", [
                                $albumId,
                                $titreM
                            ]);
                        } catch (\Exception $e) {
                            // On ignore silencieusement l'erreur de caractère
                            continue;
                        }
                    }
                }
            }

            // On vide la mémoire de Doctrine pour l'artiste
            $this->entityManager->clear();
            $io->progressAdvance();
        }

        $io->progressFinish();
        $io->success("Importation terminée ! Les morceaux incompatibles ont été ignorés pour éviter le crash.");

        return Command::SUCCESS;
    }
}