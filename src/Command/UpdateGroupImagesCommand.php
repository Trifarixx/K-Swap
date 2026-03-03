<?php

namespace App\Command;

use App\Repository\GroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-group-images',
    description: 'Met à jour les images des groupes depuis kpop_group_images.json',
)]
class UpdateGroupImagesCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GroupeRepository $groupeRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $jsonFilePath = __DIR__ . '/../../kpop_group_images.json';

        if (!file_exists($jsonFilePath)) {
            $io->error("Le fichier kpop_group_images.json est introuvable à la racine du projet.");
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
            $nomGroupe = $groupData['nom'] ?? null;
            $imageUrl = $groupData['image'] ?? null;

            if ($nomGroupe && $imageUrl) {
                // 1. On cherche par 'nomScene' car c'est là que le nom a été sauvegardé au départ
                $groupe = $this->groupeRepository->findOneBy(['nomScene' => $nomGroupe]);

                if ($groupe) {
                    // 2. On met à jour l'image
                    $groupe->setImageGrp($imageUrl);
                    
                    // 3. Optionnel : On en profite pour remplir ta nouvelle colonne nomGroupe !
                    $groupe->setNomGroupe($nomGroupe); 
                    
                    $count++;
                } else {
                    $notFound++;
                }
            }
            $io->progressAdvance();
        }

        $this->entityManager->flush();

        $io->progressFinish();
        $io->success("$count images de groupes ont été mises à jour avec succès !");
        
        if ($notFound > 0) {
            $io->warning("$notFound groupes du JSON n'ont pas été trouvés en base de données.");
        }

        return Command::SUCCESS;
    }
}