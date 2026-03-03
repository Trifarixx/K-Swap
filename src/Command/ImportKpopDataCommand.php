<?php

namespace App\Command;

use App\Entity\Groupe;
use App\Entity\Idol;
use App\Entity\MembreGroupe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-kpop-data',
    description: 'Importe les données du fichier kpop_groups.json vers la base de données',
)]
class ImportKpopDataCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        // Chemin vers le fichier JSON (à la racine du projet)
        $jsonFilePath = __DIR__ . '/../../kpop_groups.json';

        if (!file_exists($jsonFilePath)) {
            $io->error("Le fichier kpop_groups.json est introuvable à la racine du projet.");
            return Command::FAILURE;
        }

        $io->info('Lecture du fichier JSON...');
        $jsonContent = file_get_contents($jsonFilePath);
        
        // On décode le JSON en tableau associatif PHP
        $groupsData = json_decode($jsonContent, true);

        if (!$groupsData) {
            $io->error("Le fichier JSON est vide ou mal formaté.");
            return Command::FAILURE;
        }

        $io->progressStart(count($groupsData));

        foreach ($groupsData as $groupData) {
            // 1. Création du Groupe
            $groupe = new Groupe();
            // Comme Groupe hérite de Artiste, le nom se met via setNomScene
            $groupe->setNomScene($groupData['nom']); 
            $groupe->setNomFanclub($groupData['nomFanclub'] ?? null);
            $groupe->setCouleurOfficielle($groupData['couleurOfficielle'] ?? null);

            $this->entityManager->persist($groupe);

            // 2. Création des Idols et de la liaison MembreGroupe
            if (isset($groupData['membres']) && is_array($groupData['membres'])) {
                foreach ($groupData['membres'] as $membreData) {
                    
                    $idol = new Idol();
                    $idol->setNomScene($membreData['nomScene'] ?? null);
                    $idol->setNomReel($membreData['nomReel'] ?? null);

                    // Gestion de la date de naissance (conversion string vers DateTime)
                    if (!empty($membreData['dateNaissance'])) {
                        // On enlève les suffixes anglais (st, nd, rd, th) pour que strtotime comprenne bien
                        $cleanDate = preg_replace('/(st|nd|rd|th)/', '', $membreData['dateNaissance']);
                        $timestamp = strtotime($cleanDate);
                        
                        if ($timestamp) {
                            $dateObj = new \DateTime();
                            $dateObj->setTimestamp($timestamp);
                            $idol->setDateNaissance($dateObj);
                        }
                    }

                    $this->entityManager->persist($idol);

                    // 3. Création de la table de liaison avec la position
                    $membreGroupe = new MembreGroupe();
                    $membreGroupe->setGroupe($groupe);
                    $membreGroupe->setIdol($idol);
                    $membreGroupe->setPosition($membreData['position'] ?? null);

                    $this->entityManager->persist($membreGroupe);
                }
            }

            // On avance la barre de progression
            $io->progressAdvance();
        }

        // 4. On sauvegarde tout en base de données à la fin
        $io->info("\nEnregistrement dans la base de données en cours...");
        $this->entityManager->flush();

        $io->progressFinish();
        $io->success('Importation terminée avec succès !');

        return Command::SUCCESS;
    }
}