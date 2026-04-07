<?php

namespace App\Command;

use App\Repository\IdolRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DomCrawler\Crawler;

class UpdateImagesDirectCommand extends Command
{
    protected static $defaultName = 'kswap:update-direct';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private IdolRepository $idolRepository,
        private HttpClientInterface $httpClient
    ) {
        parent::__construct('kswap:update-direct');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Scraping Ultra Direct : Groupe + Prénom');

        $idolsSansImage = $this->idolRepository->findBy(['image' => null]);

        if (empty($idolsSansImage)) {
            $io->success("Toutes tes Idols ont déjà une image !");
            return Command::SUCCESS;
        }

        $updatedCount = 0;

        foreach ($idolsSansImage as $idol) {
            $nomIdol = $idol->getNomScene();
            if (!$nomIdol) continue;

            $groupes = $idol->getGroupes();
            if ($groupes->isEmpty()) {
                $io->writeln("<comment>⏭️ $nomIdol n'a pas de groupe dans ta BDD, ignoré.</comment>");
                continue;
            }

            // On prend le nom du premier groupe de l'idole
            $nomGroupe = $groupes->first()->getGroupe()->getNomGroupe();

            $io->write("Recherche de '$nomIdol' via le groupe '$nomGroupe'... ");

            $imageUrl = $this->scrapeImage($nomGroupe, $nomIdol);

            if ($imageUrl) {
                $idol->setImage($imageUrl);
                $updatedCount++;
                $io->writeln("<info>✅ Image trouvée !</info>");
            } else {
                $io->writeln("<error>❌ Introuvable</error>");
            }

            // Pause pour ne pas se faire bloquer
            sleep(1);
        }

        if ($updatedCount > 0) {
            $this->entityManager->flush();
            $io->success("Terminé ! $updatedCount images mises à jour.");
        } else {
            $io->warning("Aucune image trouvée.");
        }

        return Command::SUCCESS;
    }

    private function scrapeImage(string $nomGroupe, string $nomIdol): ?string
    {
        try {
            // 1. Recherche du groupe sur KProfiles
            $searchUrl = 'https://kprofiles.com/?s=' . urlencode($nomGroupe . ' profile');
            $response = $this->httpClient->request('GET', $searchUrl);
            $crawler = new Crawler($response->getContent());

            // 2. On prend le premier article
            $firstResult = $crawler->filter('h2.entry-title a')->first();
            if ($firstResult->count() === 0) return null;

            $articleUrl = $firstResult->attr('href');
            $articleResponse = $this->httpClient->request('GET', $articleUrl);
            $articleCrawler = new Crawler($articleResponse->getContent());

            $imgSrc = null;

            // 3. LA MÉTHODE INFAILLIBLE
            // On prend TOUTES les images de la page
            $articleCrawler->filter('img')->each(function (Crawler $imgNode) use ($nomIdol, &$imgSrc) {
                if ($imgSrc) return; // Si on l'a déjà trouvée, on arrête

                $src = $imgNode->attr('src');

                // On s'assure que c'est une vraie photo uploadée
                if (str_contains($src, 'wp-content/uploads')) {

                    // On récupère le paragraphe (<p>) ou le bloc (<div>) parent de l'image
                    $parent = $imgNode->closest('p');
                    if (!$parent || $parent->count() === 0) {
                        $parent = $imgNode->closest('div');
                    }

                    // Si le texte autour de l'image contient le prénom de l'idole
                    if ($parent && $parent->count() > 0) {
                        $text = $parent->text();
                        if (stripos($text, $nomIdol) !== false) {
                            $imgSrc = $src; // BINGO !
                        }
                    }
                }
            });

            return $imgSrc;
        } catch (\Exception $e) {
            return null;
        }
    }
}
