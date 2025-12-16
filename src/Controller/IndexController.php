<?php

namespace App\Controller;

use App\Repository\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(AvisRepository $avisRepository, Request $request): Response
    {
        // 1. Récupération des paramètres via ->query (GET)
        $page = $request->query->getInt('page', 1);
        $limit = 5;

        // 2. On récupère les données
        $avisList = $avisRepository->findFeed($page, $limit);
        $totalAvis = count($avisList);
        $pagesTotal = ceil($totalAvis / $limit);

        // 3. Détection de l'appel AJAX
        if ($request->query->has('ajax')) {
            return $this->render('index/_feed_items.html.twig', [
                'avis_list' => $avisList,
            ]);
        }

        // 4. Affichage standard
        return $this->render('index/index.html.twig', [
            'avis_list' => $avisList,
            'current_page' => $page,
            'pages_total' => $pagesTotal,
        ]);
    }
}