<?php

namespace App\Controller;

use App\Repository\ArtisteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Idol;
use App\Repository\IdolRepository;

final class ArtisteController extends AbstractController
{
    #[Route('/artiste', name: 'app_artiste')]
    public function index( IdolRepository $idolRepository): Response
    {
     return $this->render('artiste/index.html.twig', [
           'idols' => $idolRepository->findAll(),
        ]);
    }
    #[Route('/artiste/{id}', name: 'app_artiste_show')]
    public function show(Idol $idol): Response
    {
        return $this->render('artiste/show.html.twig', [
             'idol' => $idol,
        ]);
    }   

}
