<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\MorceauRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class PostController extends AbstractController
{
    #[Route('/post/nouveau', name: 'app_post_new')]
    public function new(Request $request, EntityManagerInterface $em, MorceauRepository $morceauRepository, SluggerInterface $slugger): Response
    {
        // 1. si le formulaire est soumis en POST, on traite les données
        if ($request->isMethod('POST')) {

            $avis = new Avis();
            $avis->setUser($this->getUser());

            // On récupère les données du formulaire
            $commentaire = $request->request->get('commentaire');
            $morceauId = $request->request->get('morceau_id');

            // On vérifie que le commentaire n'est pas vide (le HTML de base du content-editable peut contenir des balises vides)
            $cleanCommentaire = trim(strip_tags($commentaire));
            if (empty($cleanCommentaire)) {
                $this->addFlash('error', 'Le message ne peut pas être vide.');
                return $this->redirectToRoute('app_post_new');
            }

            $avis->setCommentaire($commentaire);

            // 3. la gestion du morceau sélectionné, obligatoire pour que le post soit lié à un album et puisse afficher la pochette dans le Feed
            if ($morceauId) {
                $morceau = $morceauRepository->find($morceauId);
                if ($morceau) {
                    $avis->setMorceau($morceau);
                    // On n'oublie pas de lier l'album pour que le feed puisse afficher la pochette
                    $avis->setDiscographie($morceau->getDiscographie());
                }
            }

            // 4. Gestion de l'image uploadée
            $imageFile = $request->files->get('media_post');

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('posts_directory'),
                        $newFilename
                    );
                    $avis->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                }
            }

            $em->persist($avis);
            $em->flush();

            $this->addFlash('success', 'Ton post a été publié avec succès !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('post/new.html.twig');
    }
}
