<?php

namespace App\Controller;

use App\Entity\Groupe;
use App\Entity\Discographie;
use App\Repository\GroupeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DiscographieController extends AbstractController
{
    // 1. La page d'accueil (Tous les groupes)
    #[Route('/discographie', name: 'app_discographie')]
    public function index(GroupeRepository $groupeRepository): Response
    {
        return $this->render('discographie/index.html.twig', [
            'groupes' => $groupeRepository->findAll(),

        ]);
    }

    // 2. La page d'un groupe (Tous ses albums)
    #[Route('/discographie/groupe/{id}', name: 'app_groupe_show')]
    public function show(Groupe $groupe): Response
    {
        return $this->render('discographie/show.html.twig', [
            'groupe' => $groupe,
        ]);
    }

    // 3. LA NOUVELLE PAGE : L'album style "Spotify" avec les étoiles !
    #[Route('/discographie/album/{id}', name: 'app_album_show')]
    public function showAlbum(Discographie $album): Response
    {
        return $this->render('discographie/album.html.twig', [
            'album' => $album,
        ]);
    }
}