<?php

namespace App\Entity;

use App\Repository\MorceauRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MorceauRepository::class)]
class Morceau
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(nullable: true)]
    private ?int $duree = null; // en secondes

    #[ORM\ManyToOne(inversedBy: 'morceaux')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Discographie $discographie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): static
    {
        $this->duree = $duree;
        return $this;
    }

    public function getDiscographie(): ?Discographie
    {
        return $this->discographie;
    }

    public function setDiscographie(?Discographie $discographie): static
    {
        $this->discographie = $discographie;
        return $this;
    }
}