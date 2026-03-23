<?php

namespace App\Entity;

use App\Repository\MorceauRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, Avis>
     */
    #[ORM\OneToMany(targetEntity: Avis::class, mappedBy: 'morceau')]
    private Collection $avis;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lienYoutube = null;

    public function __construct()
    {
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

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): static
    {
        if (!$this->avis->contains($avi)) {
            $this->avis->add($avi);
            $avi->setMorceau($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): static
    {
        if ($this->avis->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getMorceau() === $this) {
                $avi->setMorceau(null);
            }
        }

        return $this;
    }

    public function getLienYoutube(): ?string
    {
        return $this->lienYoutube;
    }

    public function setLienYoutube(?string $lienYoutube): static
    {
        $this->lienYoutube = $lienYoutube;

        return $this;
    }
}