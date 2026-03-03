<?php

namespace App\Command;

use App\Entity\Discographie;
use App\Entity\Morceau;
use App\Repository\ArtisteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-discographies',
    description: 'Importe les discographies depuis kpop_discographies.json',
)]
class ImportDiscographiesCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ArtisteRepository $artisteRepository // On utilise ArtisteRepository car Groupe hérite d'Artiste
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $jsonFilePath = __DIR__ . '/../../kpop_discographies.json';

        if (!file_exists($jsonFilePath)) {
            $io->error("Le fichier kpop_discographies.json est introuvable à la racine du projet.");
            return Command::FAILURE;
        }

        $io->info('Lecture du fichier JSON...');
        $jsonContent = file_get_contents($jsonFilePath);
        $groupsData = json_decode($jsonContent, true);

        if (!$groupsData) {
            $io->error("Le fichier JSON est vide ou mal formaté.");
            return Command::FAILURE;
        }

        $albumsCount = 0;
        $morceauxCount = 0;
        $notFound = 0;

        $io->progressStart(count($groupsData));

        foreach ($groupsData as $groupData) {
            $nomGroupe = $groupData['nomGroupe'] ?? null;

            if ($nomGroupe) {
                // On cherche l'artiste correspondant (la classe Groupe en fait partie)
                $artiste = $this->artisteRepository->findOneBy(['nomScene' => $nomGroupe]);

                if ($artiste) {
                    
                    // On boucle sur chaque album
                    foreach ($groupData['albums'] as $albumData) {
                        $discographie = new Discographie();
                        
                        // Sécurité : on coupe à 255 caractères max (ou 100 si tu n'as pas modifié tes entités)
                        $titreAlbum = substr($albumData['titre'] ?? 'Album inconnu', 0, 255);
                        
                        $discographie->setTitre($titreAlbum);
                        $discographie->setType('Album'); // Valeur par défaut, le scraper ne différencie pas EP/Single
                        $discographie->setArtiste($artiste);
                        
                        $this->entityManager->persist($discographie);
                        $albumsCount++;

                        // On boucle sur chaque morceau de l'album
                        if (isset($albumData['morceaux']) && is_array($albumData['morceaux'])) {
                            foreach ($albumData['morceaux'] as $pisteTitre) {
                                $morceau = new Morceau();
                                
                                $isTitleTrack = false;
                                
                                // On détecte si c'est la "Title track" (Chanson principale) grâce au texte de KProfiles
                                if (stripos($pisteTitre, 'title track') !== false) {
                                    $isTitleTrack = true;
                                    // On nettoie le titre pour enlever la mention "(Title track)"
                                    $pisteTitre = preg_replace('/\s*\(.*title track.*\)\s*/i', '', $pisteTitre);
                                }
                                
                                $titreMorceau = substr(trim($pisteTitre), 0, 255);
                                
                                $morceau->setTitre($titreMorceau);
                                $morceau->setIsTitleTrack($isTitleTrack);
                                $morceau->setDiscographie($discographie);
                                
                                $this->entityManager->persist($morceau);
                                $morceauxCount++;
                            }
                        }
                    }
                } else {
                    $notFound++;
                }
            }
            $io->progressAdvance();
        }

        // On enregistre tout en base de données
        $this->entityManager->flush();

        $io->progressFinish();
        $io->success("$albumsCount albums et $morceauxCount morceaux ont été importés avec succès !");
        
        if ($notFound > 0) {
            $io->warning("$notFound groupes du JSON n'ont pas été trouvés en base de données.");
        }

        return Command::SUCCESS;
    }
}