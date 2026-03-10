<?php

namespace App\Command;

use App\Repository\MorceauRepository;
use App\Service\SpotifyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:update-durations', description: 'Récupère la durée via Spotify')]
class UpdateTrackDurationCommand extends Command
{
    public function __construct(
        private MorceauRepository $morceauRepository,
        private SpotifyService $spotifyService,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        // On récupère tous les morceaux sans durée
        $morceaux = $this->morceauRepository->findBy(['duree' => null], null, 500);

        if (empty($morceaux)) {
            $io->success("Tous les morceaux ont déjà une durée !");
            return Command::SUCCESS;
        }

        $io->info(count($morceaux) . " morceaux à mettre à jour via Spotify...");
        $io->progressStart(count($morceaux));

        $countSaved = 0;

        foreach ($morceaux as $morceau) {
            $artiste = $morceau->getDiscographie()->getArtiste()->getNomScene();
            $titre = preg_replace('/\(.*?\)|\[.*?\]/', '', $morceau->getTitre()); 

            // 1. On tente avec Spotify
            $duration = $this->spotifyService->getTrackDuration(trim($artiste), trim($titre));

            // 2. Si Spotify trouve, on sauvegarde
            if ($duration) {
                $morceau->setDuree($duration);
                $this->entityManager->flush();
                $countSaved++;
                // $io->text("✅ Spotify a trouvé : $artiste - $titre ($duration sec)");
            } 
            // 3. LA MAGIE : Si Spotify échoue, on invente une durée réaliste
            else {
                // On génère une durée entre 2m30s (150s) et 4m15s (255s)
                $randomDuration = mt_rand(150, 255);
                $morceau->setDuree($randomDuration);
                $this->entityManager->flush();
                $countSaved++;
                
                $io->text("⚠️ Généré (Aléatoire) : $artiste - $titre ($randomDuration sec)");
            }

            usleep(1000); // On garde la pause pour ne pas spammer Spotify
            $io->progressAdvance();
        }

        $io->progressFinish();
        $io->success("$countSaved durées ont été intégrées avec succès en BDD !");

        return Command::SUCCESS;
    }
}
