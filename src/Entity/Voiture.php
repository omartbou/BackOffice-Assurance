<?php

namespace App\Entity;

use App\Repository\VoitureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: VoitureRepository::class)]
class Voiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['voiture:read', 'voiture:write'])]

    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['voiture:read', 'voiture:write'])]
    private ?string $numero_immatriculation = null;

    #[ORM\Column(length: 50)]
    #[Groups(['voiture:read', 'voiture:write'])]
    private ?string $voiture_usage = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['voiture:read', 'voiture:write'])]
    private ?\DateTimeInterface $date_achat = null;

    #[ORM\ManyToOne(inversedBy: 'voitures')]
    #[Groups(['voiture:read'])]
    private ?Client $client = null;

    /**
     * @var Collection<int, Devis>
     */
    #[ORM\ManyToMany(targetEntity: Devis::class, mappedBy: 'voitures')]
    private Collection $devis;

    #[ORM\Column(length: 50)]
    #[Groups(['voiture:read', 'voiture:write'])]
    private ?string $emplacement = null;

    public function __construct()
    {
        $this->devis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroImmatriculation(): ?string
    {
        return $this->numero_immatriculation;
    }

    public function setNumeroImmatriculation(string $numero_immatriculation): static
    {
        $this->numero_immatriculation = $numero_immatriculation;

        return $this;
    }

    public function getVoitureUsage(): ?string
    {
        return $this->voiture_usage;
    }

    public function setVoitureUsage(string $voiture_usage): static
    {
        $this->voiture_usage = $voiture_usage;

        return $this;
    }

    public function getDateAchat(): ?\DateTimeInterface
    {
        return $this->date_achat;
    }

    public function setDateAchat(\DateTimeInterface $date_achat): static
    {
        $this->date_achat = $date_achat;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection<int, Devis>
     */
    public function getDevis(): Collection
    {
        return $this->devis;
    }

    public function addDevi(Devis $devi): static
    {
        if (!$this->devis->contains($devi)) {
            $this->devis->add($devi);
            $devi->addVoiture($this);
        }

        return $this;
    }

    public function removeDevi(Devis $devi): static
    {
        if ($this->devis->removeElement($devi)) {
            $devi->removeVoiture($this);
        }

        return $this;
    }

    public function getEmplacement(): ?string
    {
        return $this->emplacement;
    }

    public function setEmplacement(string $emplacement): static
    {
        $this->emplacement = $emplacement;

        return $this;
    }
}
