<?php

namespace App\Command;

use App\Entity\Idol;
use App\Repository\IdolRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-jpop-idols',
    description: 'Importe les idoles J-Pop individuelles depuis jpop_groups.json',
)]
class ImportJpopIdolsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private IdolRepository $idolRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $jsonFilePath = __DIR__ . '/../../jpop_groups.json';

        if (!file_exists($jsonFilePath)) {
            $io->error("Le fichier jpop_groups.json est introuvable à la racine du projet.");
            return Command::FAILURE;
        }

        $io->info('Lecture du fichier JSON...');
        $groupsData = json_decode(file_get_contents($jsonFilePath), true);

        if (!$groupsData) {
            $io->error("Le fichier JSON est vide ou mal formaté.");
            return Command::FAILURE;
        }

        $countCreated = 0;
        $countUpdated = 0;

        // On compte le nombre total d'idoles pour la barre de progression
        $totalIdols = 0;
        foreach ($groupsData as $group) {
            if (!empty($group['membres'])) {
                $totalIdols += count($group['membres']);
            }
        }

        $io->progressStart($totalIdols);

        foreach ($groupsData as $groupData) {
            if (empty($groupData['membres'])) {
                continue;
            }

            foreach ($groupData['membres'] as $membreData) {
                $nomScene = substr(trim($membreData['nomScene'] ?? ''), 0, 255);
                
                if (!$nomScene) {
                    $io->progressAdvance();
                    continue;
                }

                // On vérifie si l'idole existe déjà via son nom de scène
                $idol = $this->idolRepository->findOneBy(['nomScene' => $nomScene]);

                if ($idol) {
                    $countUpdated++;
                } else {
                    $idol = new Idol();
                    $idol->setNomScene($nomScene);
                    $countCreated++;
                }

                // Mise à jour des autres champs
                $idol->setNomReel(substr(trim($membreData['nomReel'] ?? ''), 0, 255));
                
                if (!empty($membreData['image'])) {
                    $idol->setImage($membreData['image']);
                }

                // Traitement de la date de naissance
                if (!empty($membreData['dateNaissance'])) {
                    // On enlève les annotations entre parenthèses ex: "(Age 22)"
                    $cleanDateStr = preg_replace('/\(.*?\)/', '', $membreData['dateNaissance']);
                    $cleanDateStr = trim($cleanDateStr);
                    
                    // On essaie de convertir la chaîne en timestamp
                    $timestamp = strtotime($cleanDateStr);
                    
                    if ($timestamp !== false) {
                        $dateObj = (new \DateTime())->setTimestamp($timestamp);
                        $idol->setDateNaissance($dateObj);
                    }
                }

                $this->entityManager->persist($idol);
                $io->progressAdvance();
            }

            // On flush à chaque fin de groupe pour éviter de saturer la mémoire RAM
            $this->entityManager->flush();
            $this->entityManager->clear();
        }

        // Flush final par sécurité
        $this->entityManager->flush();

        $io->progressFinish();
        $io->success("$countCreated idoles J-Pop ont été créées et $countUpdated mises à jour !");

        return Command::SUCCESS;
    }
}