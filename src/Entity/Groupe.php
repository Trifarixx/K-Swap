<?php

namespace App\Entity;

use App\Repository\GroupeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupeRepository::class)]
class Groupe extends Artiste
{
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomGroupe = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $nomFanclub = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageGrp = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $genres = [];

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\OneToMany(mappedBy: 'groupe', targetEntity: MembreGroupe::class, orphanRemoval: true)]
    private Collection $membres;

    public function __construct()
    {
        parent::__construct();
        $this->membres = new ArrayCollection();
    }

    public function getNomGroupe(): ?string
    {
        return $this->nomGroupe;
    }

    public function setNomGroupe(?string $nomGroupe): static
    {
        $this->nomGroupe = $nomGroupe;
        return $this;
    }

    public function getNomFanclub(): ?string
    {
        return $this->nomFanclub;
    }

    public function setNomFanclub(?string $nomFanclub): static
    {
        $this->nomFanclub = $nomFanclub;
        return $this;
    }

    public function getImageGrp(): ?string
    {
        return $this->imageGrp;
    }

    public function setImageGrp(?string $imageGrp): static
    {
        $this->imageGrp = $imageGrp;
        return $this;
    }

    public function getGenres(): array
    {
        return $this->genres ?? [];
    }

    public function setGenres(?array $genres): static
    {
        $this->genres = $genres;
        return $this;
    }

    public function addGenre(string $genre): static
    {
        $currentGenres = $this->getGenres();
        if (!in_array($genre, $currentGenres, true)) {
            $currentGenres[] = $genre;
            $this->setGenres($currentGenres);
        }
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getMembres(): Collection
    {
        return $this->membres;
    }
}
