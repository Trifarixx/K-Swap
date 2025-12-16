<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')] // Sécurité : Seul un membre connecté peut poster
class PostController extends AbstractController
{
    #[Route('/post/nouveau', name: 'app_post_new')]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        // 1. On prépare un nouvel objet Avis vide
        $avis = new Avis();
        
        // 2. On lie automatiquement l'utilisateur connecté
        $avis->setUser($this->getUser());

        // 3. On crée le formulaire
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        // 4. Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            
            // --- GESTION DE L'IMAGE (Upload) ---
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                // On nettoie le nom du fichier (sécurité)
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    // On déplace le fichier dans le dossier public/uploads/posts
                    $imageFile->move(
                        $this->getParameter('posts_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // (Optionnel) Ajouter un message flash d'erreur ici
                }

                // On enregistre le nom du fichier dans l'entité
                $avis->setImage($newFilename);
            }

            // --- NETTOYAGE LOGIQUE ---
            // Si l'utilisateur n'a pas choisi d'album, on s'assure que la note est vide
            if (!$avis->getDiscographie()) {
                $avis->setNote(0);
            }

            // 5. On sauvegarde en base de données
            $em->persist($avis);
            $em->flush();

            // 6. On ajoute un message de succès et on redirige vers l'accueil
            $this->addFlash('success', 'Votre post a été publié !');
            return $this->redirectToRoute('app_home');
        }

        // Affiche la vue avec le formulaire
        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}