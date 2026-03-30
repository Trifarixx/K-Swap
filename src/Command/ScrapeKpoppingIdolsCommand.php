<?php

namespace App\Command;

use App\Entity\Groupe;
use App\Entity\Idol;
use App\Entity\MembreGroupe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:scrape-idols',
    description: 'Remplit automatiquement les groupes vides en aspirant les données cachées sur Kpopping.',
)]
class ScrapeKpoppingIdolsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private HttpClientInterface $client
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('🚀 Démarrage de l\'extraction ciblée des Groupes (Mode Rapide)');

        $idolRepo = $this->entityManager->getRepository(Idol::class);
        $groupeRepo = $this->entityManager->getRepository(Groupe::class);

        // On récupère TOUS les groupes
        $groupes = $groupeRepo->findAll();

        $idolsAjoutes = 0;
        $idolsMisAJour = 0;
        $groupesTraites = 0;

        $slugger = new AsciiSlugger();

        foreach ($groupes as $groupe) {

            // On ignore les groupes qui ont déjà des membres
            if (count($groupe->getMembres()) > 0) {
                continue;
            }

            $nomGroupe = $groupe->getNomGroupe();

            // Création d'une URL propre (ex: "Girls' Generation" -> "girls-generation")
            $slugGroupe = $slugger->slug($nomGroupe)->lower()->toString();
            $url = "https://kpopping.com/profiles/group/" . $slugGroupe;

            try {
                $response = $this->client->request('GET', $url);

                // Si le groupe n'est pas sur Kpopping (ex: J-Pop), on passe au suivant en silence
                if ($response->getStatusCode() !== 200) {
                    $io->text("⏭️ Ignoré : $nomGroupe (Non trouvé sur Kpopping)");
                    continue;
                }

                $io->section("🔍 Recherche des membres pour : $nomGroupe");

                $html = $response->getContent();

                // Extraction du bloc JSON caché
                if (preg_match('/"members":(\[.*?\]),"formerMembers"/s', $html, $matches)) {

                    $jsonString = str_replace('\"', '"', $matches[1]);
                    $membersData = json_decode($jsonString, true);

                    if (!$membersData) {
                        $io->error("Impossible de décoder les données de $nomGroupe.");
                        continue;
                    }

                    $io->text("✅ " . count($membersData) . " membres trouvés ! Ajout en cours...");

                    foreach ($membersData as $member) {
                        $nomScene = $member['name'] ?? null;

                        if (!$nomScene) continue;

                        $idol = $idolRepo->findOneBy(['nomScene' => $nomScene]);
                        $isNew = false;

                        if (!$idol) {
                            $idol = new Idol();
                            $idol->setNomScene($nomScene);
                            $isNew = true;
                        }

                        if (!empty($member['koreanName']) && !$idol->getNomReel()) {
                            $idol->setNomReel($member['koreanName']);
                        }

                        if (!empty($member['birthday'])) {
                            try {
                                $dateNaissance = new \DateTime($member['birthday']);
                                $idol->setDateNaissance($dateNaissance);
                            } catch (\Exception $e) {
                                // Date invalide, on ignore
                            }
                        }

                        if (!empty($member['image'])) {
                            $cleanImage = explode('?', $member['image'])[0];
                            // Sécurité pour s'assurer que l'URL de l'image est absolue
                            $finalUrl = str_starts_with($cleanImage, 'http') ? $cleanImage : 'https://cdn.kpopping.com' . $cleanImage;
                            $idol->setImage($finalUrl);
                        }

                        $this->entityManager->persist($idol);

                        // Sécurité anti-doublon pour le lien MembreGroupe
                        $estDejaMembre = false;
                        foreach ($idol->getGroupes() as $membreGroupe) {
                            if ($membreGroupe->getGroupe() === $groupe) {
                                $estDejaMembre = true;
                                break;
                            }
                        }

                        if (!$estDejaMembre) {
                            $lien = new MembreGroupe();
                            $lien->setIdol($idol);
                            $lien->setGroupe($groupe);
                            $this->entityManager->persist($lien);
                        }

                        if ($isNew) {
                            $idolsAjoutes++;
                            $io->text("   ➕ $nomScene");
                        } else {
                            $idolsMisAJour++;
                        }
                    }

                    $this->entityManager->flush();
                    $groupesTraites++;

                    // Petite pause de courtoisie pour le serveur
                    usleep(500000);
                } else {
                    // On remplace le gros warning par un petit texte discret
                    $io->text("   ⏭️ Ignoré : Données introuvables (Soft 404 ou groupe vide).");
                }
            } catch (\Exception $e) {
                $io->text("⏭️ Erreur/Introuvable pour : $nomGroupe");
            }
        }

        $io->success([
            "Mission accomplie !",
            "Groupes remplis : $groupesTraites",
            "Idols créés : $idolsAjoutes",
            "Idols mis à jour : $idolsMisAJour"
        ]);

        return Command::SUCCESS;
    }
}
