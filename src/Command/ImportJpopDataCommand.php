<?php

namespace App\Command;

use App\Entity\Groupe;
use App\Entity\Idol;
use App\Repository\GroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-jpop-data',
    description: 'Importe les groupes J-Pop depuis jpop_groups.json',
)]
class ImportJpopDataCommand extends Command
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
        $jsonFilePath = __DIR__ . '/../../jpop_groups.json';

        if (!file_exists($jsonFilePath)) {
            $io->error("Le fichier jpop_groups.json est introuvable.");
            return Command::FAILURE;
        }

        $groupsData = json_decode(file_get_contents($jsonFilePath), true);
        if (!$groupsData) {
            $io->error("Le fichier JSON est vide ou mal formaté.");
            return Command::FAILURE;
        }

        $countCreated = 0;
        $countUpdated = 0;

        $io->progressStart(count($groupsData));

        foreach ($groupsData as $groupData) {
            $nom = substr($groupData['nom'] ?? 'Inconnu', 0, 255);
            
            // On cherche le groupe (soit par nomGroupe, soit par nomScene)
            $groupe = $this->groupeRepository->findOneBy(['nomGroupe' => $nom]);
            if (!$groupe) {
                $groupe = $this->groupeRepository->findOneBy(['nomScene' => $nom]);
            }

            if ($groupe) {
                // Le groupe existe déjà (ex: groupe de K-Pop qui fait de la J-Pop)
                $groupe->addGenre('J-Pop');
                $countUpdated++;
            } else {
                // C'est un nouveau groupe exclusivement J-Pop
                $groupe = new Groupe();
                $groupe->setNomScene($nom);
                $groupe->setNomGroupe($nom);
                $groupe->addGenre('J-Pop');
                $groupe->setNomFanclub(substr($groupData['nomFanclub'] ?? '', 0, 50));
                
                // On met l'image si elle est dispo
                if (!empty($groupData['image'])) {
                    $groupe->setImageGrp($groupData['image']);
                }

                $this->entityManager->persist($groupe);
                $countCreated++;
                
                // (Optionnel) Tu pourrais aussi boucler sur les idoles ici comme pour la K-pop
            }

            $io->progressAdvance();
        }

        // On sauvegarde tout
        $this->entityManager->flush();

        $io->progressFinish();
        $io->success("$countCreated nouveaux groupes J-Pop créés et $countUpdated groupes existants mis à jour avec le genre J-Pop !");

        return Command::SUCCESS;
    }
}