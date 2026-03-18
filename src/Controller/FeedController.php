<?php

namespace App\Controller;

use App\Repository\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use ColorThief\ColorThief;

class FeedController extends AbstractController
{
    #[Route('/accueil', name: 'app_accueil')]
    public function index(AvisRepository $avisRepository): Response
    {
        // 1. On récupère la liste des avis (ici, tous, mais tu peux limiter)
        $avisList = $avisRepository->findAll();

        // 2. On prépare un tableau pour stocker les avis "enrichis" avec leurs couleurs
        $avisWithColors = [];

        // 3. On boucle sur chaque avis pour extraire les couleurs de sa pochette
        foreach ($avisList as $avis) {
            $discographie = $avis->getDiscographie();
            $colors = []; // Par défaut, pas de couleurs spécifiques

            // Si l'avis est lié à une discographie et que celle-ci a une pochette
            if ($discographie && $discographie->getPochette()) {
                // Chemin complet vers l'image sur ton serveur (important pour ColorThief)
                $imagePath = $this->getParameter('kernel.project_dir') . '/public/images/albums/' . $discographie->getPochette();

                // On s'assure que le fichier existe avant d'essayer de le lire
                if (file_exists($imagePath)) {
                    // On récupère une palette de 5 couleurs dominantes
                    $palette = ColorThief::getPalette($imagePath, 5);

                    // Si on a récupéré une palette
                    if ($palette && count($palette) >= 2) {
                        // On sélectionne deux couleurs pour le dégradé (par exemple, la 1ère et la 2ème)
                        $color1 = $palette[0];
                        $color2 = $palette[1];

                        // On formate les couleurs en chaînes RVB pour CSS (ex: "rgb(255, 109, 179)")
                        $colors = [
                            'couleur1' => "rgb({$color1[0]}, {$color1[1]}, {$color1[2]})",
                            'couleur2' => "rgb({$color2[0]}, {$color2[1]}, {$color2[2]})",
                        ];
                    }
                }
            }

            // 4. On stocke l'avis et ses couleurs dans le nouveau tableau
            $avisWithColors[] = [
                'avis' => $avis,
                'colors' => $colors, // Sera un tableau avec 'couleur1'/'couleur2' ou vide
            ];
        }

        // 5. On envoie ce tableau "enrichi" à Twig
        return $this->render('accueil/index.html.twig', [
            'avis_list_with_colors' => $avisWithColors,
        ]);
    }
}
