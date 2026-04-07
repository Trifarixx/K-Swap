<?php

namespace App\Controller;

use App\Repository\MorceauRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    #[Route('/api/search/morceaux', name: 'api_search_morceaux', methods: ['GET'])]
    public function searchMorceaux(Request $request, MorceauRepository $morceauRepository): JsonResponse
    {
        $query = trim($request->query->get('q', ''));

        if (strlen($query) < 2) {
            return new JsonResponse([]);
        }

        $qb = $morceauRepository->createQueryBuilder('m')
            ->join('m.discographie', 'd')
            ->join('d.artiste', 'a');

        // 1. On découpe la recherche par les espaces (ex: "aespa supernova" devient ["aespa", "supernova"])
        $mots = explode(' ', $query);

        // 2. On boucle sur chaque mot pour l'ajouter à la requête
        foreach ($mots as $index => $mot) {
            // Le mot doit se trouver dans le titre OU l'album OU l'artiste
            $qb->andWhere(
                $qb->expr()->orX(
                    'm.titre LIKE :mot' . $index,
                    'd.titre LIKE :mot' . $index,
                    'a.nomScene LIKE :mot' . $index
                )
            )
                ->setParameter('mot' . $index, '%' . $mot . '%');
        }

        $qb->setMaxResults(10);

        $morceaux = $qb->getQuery()->getResult();

        $results = [];
        foreach ($morceaux as $m) {
            $results[] = [
                'id' => $m->getId(),
                'titre' => $m->getTitre(),
                'album' => $m->getDiscographie()->getTitre(),
                'album_id' => $m->getDiscographie()->getId(),
                'artiste' => $m->getDiscographie()->getArtiste()->getNomScene(),
                'pochette' => $m->getDiscographie()->getPochette()
            ];
        }

        return new JsonResponse($results);
    }
}
