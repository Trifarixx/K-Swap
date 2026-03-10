<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Discographie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AvisController extends AbstractController
{
    #[Route('/avis/add/{id}', name: 'app_avis_add')]
    public function addAvis(Discographie $discographie, Request $request, EntityManagerInterface $em): Response
    {
        // 1. Sécurité : On vérifie que l'utilisateur est bien connecté
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', 'Tu dois être connecté pour noter un album !');
            return $this->redirectToRoute('app_login'); // Redirige vers ta page de connexion
        }

        // 2. On récupère la note passée dans l'URL (?note=5)
        $note = $request->query->get('note');

        if ($note) {
            // 3. On crée le nouvel Avis
            $avis = new Avis();
            $avis->setNote((int) $note);
            $avis->setDiscographie($discographie);
            $avis->setUser($user);
            $avis->setDateCreation(new \DateTime()); // Obligatoire selon ton schéma SQL
            
            // 4. On sauvegarde en base de données
            $em->persist($avis);
            $em->flush();

            $this->addFlash('success', 'Ta note a bien été enregistrée !');
        }

        // 5. On redirige l'utilisateur d'où il vient (ou vers la page de l'album)
        // Remplace 'app_discographie_index' par le nom de la route où s'affichent tes cartes
        return $this->redirectToRoute('app_discographie_index'); 
    }
}