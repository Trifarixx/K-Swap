<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Morceau;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AvisController extends AbstractController
{
    #[Route('/avis/ajouter/morceau/{id}', name: 'app_avis_ajouter_morceau', methods: ['POST'])]
    public function ajouterAvisMorceau(Morceau $morceau, Request $request, EntityManagerInterface $em): Response
    {
        // 1. Sécurité : On vérifie que l'utilisateur est bien connecté
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Tu dois être connecté pour poster un avis.');
            // On le renvoie vers la page de l'album (ou vers la page de connexion)
            return $this->redirectToRoute('app_album_show', ['id' => $morceau->getDiscographie()->getId()]);
        }

        // 2. On récupère les données envoyées par ton formulaire HTML
        $note = $request->request->get('note');
        $commentaire = $request->request->get('commentaire');

        // 3. On vérifie que les champs obligatoires sont bien remplis
        if ($note && $commentaire) {

            // 4. On crée le nouvel Avis !
            $avis = new Avis();
            $avis->setNote((int) $note);
            $avis->setCommentaire($commentaire);
            $avis->setMorceau($morceau);
            $avis->setUser($user);

            // LES DEUX LIGNES MAGIQUES AJOUTÉES ICI :
            // On relie l'avis à l'album du morceau
            $avis->setDiscographie($morceau->getDiscographie());
            // On sauvegarde le nom de l'image de la pochette
            $avis->setImage($morceau->getDiscographie()->getPochette());

            $em->persist($avis);
            $em->flush();

            $this->addFlash('success', 'Ton avis a été posté avec succès ! 🌟');
        } else {
            $this->addFlash('error', 'Il manque la note ou le commentaire.');
        }

        // 6. On redirige l'utilisateur vers la page de l'album où il était
        return $this->redirectToRoute('app_album_show', [
            'id' => $morceau->getDiscographie()->getId()
        ]);
    }
}
