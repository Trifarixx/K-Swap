<?php

namespace App\Controller;

use App\Repository\GroupeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DiscographieController extends AbstractController
{
    #[Route('/discographie', name: 'app_discographie')]
    public function index(GroupeRepository $groupeRepository): Response
    {
        return $this->render('discographie/index.html.twig', [
            'groupes' => $groupeRepository->findAll(),
        ]);
    }
}