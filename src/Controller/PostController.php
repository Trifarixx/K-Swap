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
        // 1. SI LA REQUÊTE EST EN POST, C'EST QUE LE FORMULAIRE A ÉTÉ SOUMIS
        if ($request->isMethod('POST')) {

            $avis = new Avis();
            $avis->setUser($this->getUser());

            // 2. RÉCUPÉRATION DES DONNÉES ENVOYÉES MANUELLEMENT
            $commentaire = $request->request->get('commentaire');
            $morceauId = $request->request->get('morceau_id');

            // On vérifie que le commentaire n'est pas vide (le HTML de base du contenteditable peut contenir des balises vides)
            $cleanCommentaire = trim(strip_tags($commentaire));
            if (empty($cleanCommentaire)) {
                $this->addFlash('error', 'Le message ne peut pas être vide.');
                return $this->redirectToRoute('app_post_new');
            }

            $avis->setCommentaire($commentaire);

            // 3. GESTION DE LA MUSIQUE LIÉE (Si l'utilisateur en a sélectionné une)
            if ($morceauId) {
                $morceau = $morceauRepository->find($morceauId);
                if ($morceau) {
                    $avis->setMorceau($morceau);
                    // On n'oublie pas de lier l'album pour que ton Feed affiche la pochette !
                    $avis->setDiscographie($morceau->getDiscographie());
                }
            } else {
                // Tu peux rendre ça obligatoire ou non dans ton PHP, 
                // mais on a mis une sécurité JS tout à l'heure pour obliger le choix.
                $this->addFlash('error', 'Tu dois sélectionner une musique.');
                return $this->redirectToRoute('app_post_new');
            }

            // 4. GESTION DE L'IMAGE UPLOADÉE (Si on rajoute un input file plus tard)
            // Dans notre nouveau design, on n'a pas mis de bouton d'upload d'image, 
            // mais je garde ton code au cas où tu voudrais le remettre plus tard dans le Twig : <input type="file" name="imageFile">
            $imageFile = $request->files->get('imageFile');

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
