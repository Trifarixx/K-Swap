<?php

namespace App\Entity;

use App\Repository\MembreGroupeRepository;
use Doctrine\ORM\Mapping as ORM;

// C'est une table de liaison avec attributs (Position)
#[ORM\Entity(repositoryClass: MembreGroupeRepository::class)]
class MembreGroupe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'groupes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Idol $idol = null;

    #[ORM\ManyToOne(inversedBy: 'membres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Groupe $groupe = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $position = null; // Leader, Rapper, Vocal...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdol(): ?Idol
    {
        return $this->idol;
    }

    public function setIdol(?Idol $idol): static
    {
        $this->idol = $idol;
        return $this;
    }

    public function getGroupe(): ?Groupe
    {
        return $this->groupe;
    }

    public function setGroupe(?Groupe $groupe): static
    {
        $this->groupe = $groupe;
        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): static
    {
        $this->position = $position;
        return $this;
    }
}