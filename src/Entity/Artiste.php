<?php

namespace App\Entity;

use App\Repository\ArtisteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

// Héritage JOINED : Une table pour Artiste, une table pour Groupe
#[ORM\Entity(repositoryClass: ArtisteRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'discr_type', type: 'string')]
#[ORM\DiscriminatorMap(['artiste' => Artiste::class, 'groupe' => Groupe::class])]
class Artiste
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nomScene = null;

    #[ORM\OneToMany(mappedBy: 'artiste', targetEntity: Discographie::class)]
    private Collection $discographies;

    #[ORM\OneToMany(mappedBy: 'artiste', targetEntity: Evenement::class)]
    private Collection $evenements;

    public function __construct()
    {
        $this->discographies = new ArrayCollection();
        $this->evenements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomScene(): ?string
    {
        return $this->nomScene;
    }

    public function setNomScene(string $nomScene): static
    {
        $this->nomScene = $nomScene;
        return $this;
    }

    public function getDiscographies(): Collection
    {
        return $this->discographies;
    }

    public function getEvenements(): Collection
    {
        return $this->evenements;
    }
}