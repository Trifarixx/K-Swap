<?php

namespace App\Entity;

use App\Repository\DiscographieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscographieRepository::class)]
class Discographie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateSortie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pochette = null;

    #[ORM\Column(length: 20)]
    private ?string $type = null; // Album, EP, Single

    #[ORM\ManyToOne(inversedBy: 'discographies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Artiste $artiste = null;

    #[ORM\OneToMany(mappedBy: 'discographie', targetEntity: Morceau::class, orphanRemoval: true)]
    private Collection $morceaux;

    #[ORM\OneToMany(mappedBy: 'discographie', targetEntity: Avis::class)]
    private Collection $avis;

    public function __construct()
    {
        $this->morceaux = new ArrayCollection();
        $this->avis = new ArrayCollection();
    }

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

    public function getDateSortie(): ?\DateTimeInterface
    {
        return $this->dateSortie;
    }

    public function setDateSortie(?\DateTimeInterface $dateSortie): static
    {
        $this->dateSortie = $dateSortie;
        return $this;
    }

    public function getPochette(): ?string
    {
        return $this->pochette;
    }

    public function setPochette(?string $pochette): static
    {
        $this->pochette = $pochette;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getArtiste(): ?Artiste
    {
        return $this->artiste;
    }

    public function setArtiste(?Artiste $artiste): static
    {
        $this->artiste = $artiste;
        return $this;
    }

    public function getMorceaux(): Collection
    {
        return $this->morceaux;
    }

    public function addMorceau(Morceau $morceau): static
    {
        if (!$this->morceaux->contains($morceau)) {
            $this->morceaux->add($morceau);
            $morceau->setDiscographie($this);
        }

        return $this;
    }   

    public function removeMorceau(Morceau $morceau): static
    {
        if ($this->morceaux->removeElement($morceau)) {
            // set the owning side to null (unless already changed)
            if ($morceau->getDiscographie() === $this) {
                $morceau->setDiscographie(null);
            }
        }

        return $this;
    }

    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): static
    {
        if (!$this->avis->contains($avi)) {
            $this->avis->add($avi);
            $avi->setDiscographie($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): static
    {
        if ($this->avis->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getDiscographie() === $this) {
                $avi->setDiscographie(null);
            }
        }

        return $this;
    }
}