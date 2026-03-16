<?php

namespace App\Controller;

use App\Entity\Morceau;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class YoutubeController extends AbstractController
{
    #[Route('/api/youtube/{id}', name: 'api_youtube_search', methods: ['GET'])]
    public function search(Morceau $morceau, EntityManagerInterface $em): JsonResponse
    {
        // 1. Si le lien existe déjà dans la BDD, on le renvoie direct (0 seconde d'attente !)
        if ($morceau->getLienYoutube() !== null) {
            return $this->json(['url' => $morceau->getLienYoutube()]);
        }

        // 2. Sinon, on prépare la recherche
        $artiste = $morceau->getDiscographie()->getArtiste()->getNomScene();
        $titre = $morceau->getTitre();

        // On sécurise le texte pour la ligne de commande
        $query = escapeshellarg($artiste . ' ' . $titre . ' Topic');
        $scriptPath = escapeshellarg($this->getParameter('kernel.project_dir') . '/search_yt.js');

        // 3. PHP réveille Node.js en arrière-plan et attend sa réponse
        $command = "node $scriptPath $query";
        $output = trim(shell_exec($command));

        // 4. Si Node.js a trouvé un lien http
        if (str_starts_with($output, 'http')) {
            $morceau->setLienYoutube($output);
            $em->flush();

            return $this->json(['url' => $output]);
        }

        // 5. Échec
        return $this->json(['error' => 'Vidéo introuvable'], 404);
    }
}
