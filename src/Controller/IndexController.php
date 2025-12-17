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
        $page = $request->query->getInt('page', 1);
        $limit = 5;

        // On compte le total pour savoir quand s'arrêter
        $totalAvis = $avisRepository->count([]);
        $pagesTotal = ceil($totalAvis / $limit);

        $avisList = $avisRepository->findFeed($page, $limit);

        // Si la requête vient de Turbo, on renvoie seulement la liste des articles
        if ($request->headers->get('Turbo-Frame')) {
            return $this->render('index/_feed_items.html.twig', [
                'avis_list' => $avisList,
                'current_page' => $page,
                'pages_total' => $pagesTotal,
            ]);
        }

        // Sinon (premier chargement), on renvoie la page complète
        return $this->render('index/index.html.twig', [
            'avis_list' => $avisList,
            'current_page' => $page,
            'pages_total' => $pagesTotal,
        ]);
    }
}