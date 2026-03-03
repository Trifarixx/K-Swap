<?php

namespace App\Command;

use App\Repository\IdolRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-idol-images',
    description: 'Met à jour les images des idoles depuis kpop_images.json',
)]
class UpdateIdolImagesCommand extends Command
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
        
        // Chemin vers ton fichier JSON (assure-toi qu'il est bien à la racine du projet)
        $jsonFilePath = __DIR__ . '/../../kpop_images.json';

        if (!file_exists($jsonFilePath)) {
            $io->error("Le fichier kpop_images.json est introuvable à la racine du projet.");
            return Command::FAILURE;
        }

        $io->info('Lecture du fichier JSON...');
        $jsonContent = file_get_contents($jsonFilePath);
        $groupsData = json_decode($jsonContent, true);

        if (!$groupsData) {
            $io->error("Le fichier JSON est vide ou mal formaté.");
            return Command::FAILURE;
        }

        $count = 0;
        $notFound = 0;

        $io->progressStart(count($groupsData));

        foreach ($groupsData as $groupData) {
            if (isset($groupData['membres']) && is_array($groupData['membres'])) {
                foreach ($groupData['membres'] as $membreData) {
                    
                    $nomScene = $membreData['nomScene'] ?? null;
                    $imageUrl = $membreData['image'] ?? null;

                    if ($nomScene && $imageUrl) {
                        // On cherche l'idole existante dans la BDD grâce à son nom de scène
                        $idol = $this->idolRepository->findOneBy(['nomScene' => $nomScene]);

                        if ($idol) {
                            // On met à jour l'image
                            $idol->setImage($imageUrl);
                            $count++;
                        } else {
                            $notFound++;
                        }
                    }
                }
            }
            $io->progressAdvance();
        }

        // On envoie toutes les modifications à la base de données
        $this->entityManager->flush();

        $io->progressFinish();
        $io->success("$count images d'idoles ont été mises à jour avec succès !");
        
        if ($notFound > 0) {
            $io->warning("$notFound idoles du JSON n'ont pas été trouvées en base de données (noms différents ?).");
        }

        return Command::SUCCESS;
    }
}