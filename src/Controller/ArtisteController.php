<?php
namespace App\Controller;

use App\Entity\Groupe;
use App\Entity\Idol;
use App\Repository\IdolRepository;
use App\Repository\GroupeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArtisteController extends AbstractController
{
    // Page index : liste des groupes
    #[Route('/artiste', name: 'app_artiste')]
    public function index(GroupeRepository $groupeRepository): Response
    {
        // Récupère tous les groupes
        $groupes = $groupeRepository->findAll();
       

        return $this->render('artiste/index.html.twig', [
            'groupes' => $groupeRepository->findAll(),
           
        
        ]);
    }

    #[Route('/artiste/groupe/{id}', name: 'app_artiste_groupe')]
public function groupe(Groupe $groupe): Response
{
    // Récupère tous les membres du groupe
    $membres = $groupe->getMembres(); // Collection de MembreGroupe

    return $this->render('artiste/groupe.html.twig', [
        'groupe' => $groupe,
        'membres' => $membres,
    ]);
}

    // Page artiste : fiche complète d’un idol
  
    #[Route('/artiste/{id}', name: 'app_artiste_show')]
public function show(?Idol $idol): Response
{
    if (!$idol) {
        throw $this->createNotFoundException('Idol non trouvé.');
    }

    return $this->render('artiste/show.html.twig', [
        'idol' => $idol,
    ]);
}
}