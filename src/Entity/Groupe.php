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
    // Pas d'ID ici car il hérite de Artiste

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $nomFanclub = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $couleurOfficielle = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\OneToMany(mappedBy: 'groupe', targetEntity: MembreGroupe::class, orphanRemoval: true)]
    private Collection $membres;

    public function __construct()
    {
        parent::__construct();
        $this->membres = new ArrayCollection();
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

    public function getCouleurOfficielle(): ?string
    {
        return $this->couleurOfficielle;
    }

    public function setCouleurOfficielle(?string $couleurOfficielle): static
    {
        $this->couleurOfficielle = $couleurOfficielle;
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