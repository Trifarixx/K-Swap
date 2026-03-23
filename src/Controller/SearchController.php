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
        $query = $request->query->get('q', '');

        if (strlen($query) < 2) {
            return new JsonResponse([]);
        }

        $qb = $morceauRepository->createQueryBuilder('m')
            ->join('m.discographie', 'd')
            ->join('d.artiste', 'a')
            ->where('m.titre LIKE :query')
            ->orWhere('d.titre LIKE :query')
            ->orWhere('a.nomScene LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->setMaxResults(10);

        $morceaux = $qb->getQuery()->getResult();

        $results = [];
        foreach ($morceaux as $m) {
            $results[] = [
                'id' => $m->getId(),
                'titre' => $m->getTitre(),
                'album' => $m->getDiscographie()->getTitre(),
                'artiste' => $m->getDiscographie()->getArtiste()->getNomScene(),
                'pochette' => $m->getDiscographie()->getPochette()
            ];
        }

        return new JsonResponse($results);
    }
}
