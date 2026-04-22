<?php

namespace App\Controller;

use App\Entity\Commentaire;
use Symfony\Component\Security\Http\Attribute\IsGranted;
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
        // On vérifie que l'utilisateur est bien connecté
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Tu dois être connecté pour poster un avis.');
            // On le renvoie vers la page de l'album ou vers la page de connexion
            return $this->redirectToRoute('app_album_show', ['id' => $morceau->getDiscographie()->getId()]);
        }

        // On récupère les données envoyées par le formulaire
        $note = $request->request->get('note');
        $commentaire = $request->request->get('commentaire');

        // On vérifie que les champs obligatoires sont bien remplis
        if ($note && $commentaire) {

            // On crée le nouvel Avis 
            $avis = new Avis();
            $avis->setNote((int) $note);
            $avis->setCommentaire($commentaire);
            $avis->setMorceau($morceau);
            $avis->setUser($user);

            // On relie l'avis à l'album du morceau
            $avis->setDiscographie($morceau->getDiscographie());
            // On sauvegarde le nom de l'image de la pochette
            $avis->setImage($morceau->getDiscographie()->getPochette());

            $em->persist($avis);
            $em->flush();

            $this->addFlash('success', 'Ton avis a été posté avec succès ! ');
        } else {
            $this->addFlash('error', 'Il manque la note ou le commentaire.');
        }

        // On redirige l'utilisateur vers la page de l'album où il était
        return $this->redirectToRoute('app_album_show', [
            'id' => $morceau->getDiscographie()->getId()
        ]);
    }

    #[Route('/avis/{id}/commenter', name: 'app_avis_commenter', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function commenter(Avis $avis, Request $request, EntityManagerInterface $em): Response
    {
        $contenu = trim((string) $request->request->get('contenu', ''));

        if ($contenu === '') {
            $this->addFlash('error', 'Le commentaire ne peut pas être vide.');
            return $this->redirectToRoute('app_home');
        }

        $commentaire = new Commentaire();
        $commentaire->setContenu($contenu);
        $commentaire->setUser($this->getUser());
        $commentaire->setAvis($avis);

        $em->persist($commentaire);
        $em->flush();

        $this->addFlash('success', 'Commentaire publié.');
        return $this->redirectToRoute('app_home');
    }
}
