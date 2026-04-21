<?php

namespace App\Controller;

use App\Repository\GroupeRepository;
use App\Repository\DiscographieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

class DecouvrirController extends AbstractController
{
    /**
     * Tire au sort 50/50 entre la page d'un groupe et un album, puis redirige.
     * Fallback sur l'autre type si la table choisie est vide, puis sur app_home si les deux le sont.
     */
    #[Route('/decouvrir', name: 'app_decouvrir')]
    public function decouvrir(
        GroupeRepository $groupeRepository,
        DiscographieRepository $discographieRepository
    ): RedirectResponse {
        if (random_int(0, 1) === 0) {
            // Page groupe aléatoire
            $id = $groupeRepository->findRandomId();
            if ($id !== null) {
                return $this->redirectToRoute('app_groupe_show', ['id' => $id]);
            }
            // Fallback album
            $id = $discographieRepository->findRandomId();
            if ($id !== null) {
                return $this->redirectToRoute('app_album_show', ['id' => $id]);
            }
        } else {
            // Album aléatoire
            $id = $discographieRepository->findRandomId();
            if ($id !== null) {
                return $this->redirectToRoute('app_album_show', ['id' => $id]);
            }
            // Fallback groupe
            $id = $groupeRepository->findRandomId();
            if ($id !== null) {
                return $this->redirectToRoute('app_groupe_show', ['id' => $id]);
            }
        }

        return $this->redirectToRoute('app_home');
    }
}