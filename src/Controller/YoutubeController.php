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
        if ($morceau->getLienYoutube()) {
            return new JsonResponse(['url' => $morceau->getLienYoutube()]);
        }

        $artiste = $morceau->getDiscographie()->getArtiste()->getNomScene();
        $titre = $morceau->getTitre();
        $searchTerm = $artiste . ' ' . $titre . ' official audio OR lyrics';

        $query = urlencode($searchTerm);
        $youtubeSearchUrl = 'https://www.youtube.com/results?search_query=' . $query;

        $options = [
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n" .
                    "Accept-Language: fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7\r\n"
            ]
        ];
        $context = stream_context_create($options);
        $html = @file_get_contents($youtubeSearchUrl, false, $context);

        if ($html && preg_match('/"videoId":"([a-zA-Z0-9_-]{11})"/', $html, $matches)) {
            $finalUrl = 'https://www.youtube.com/watch?v=' . $matches[1];
            $morceau->setLienYoutube($finalUrl);
            $em->flush();
            return new JsonResponse(['url' => $finalUrl]);
        }

        return new JsonResponse(['error' => 'Vidéo introuvable'], 404);
    }
}
