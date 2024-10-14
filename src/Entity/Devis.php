<?php

namespace App\Entity;

use App\Repository\DevisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: DevisRepository::class)]
class Devis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['devis:read', 'devis:write'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 36)]
    #[Groups(['devis:read', 'devis:write'])]
    private ?string $numero = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['devis:read', 'devis:write'])]
    private ?\DateTimeInterface $date_effet = null;

    #[ORM\Column]
    #[Groups(['devis:read', 'devis:write'])]
    private ?float $prix = null;

    #[ORM\Column(length: 50)]
    #[Groups(['devis:read', 'devis:write'])]
    private ?string $frequence_prix = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'devis')]
    #[Groups(['devis:read'])]
    private ?Client $client = null;

    /**
     * @var Collection<int, Voiture>
     */
    #[ORM\ManyToMany(targetEntity: Voiture::class, inversedBy: 'devis')]
    private Collection $voitures;

    public function __construct()
    {
        $this->voitures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getDateEffet(): ?\DateTimeInterface
    {
        return $this->date_effet;
    }

    public function setDateEffet(\DateTimeInterface $date_effet): static
    {
        $this->date_effet = $date_effet;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getFrequencePrix(): ?string
    {
        return $this->frequence_prix;
    }

    public function setFrequencePrix(string $frequence_prix): static
    {
        $this->frequence_prix = $frequence_prix;

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
     * @return Collection<int, Voiture>
     */
    public function getVoitures(): Collection
    {
        return $this->voitures;
    }

    public function addVoiture(Voiture $voiture): static
    {
        if (!$this->voitures->contains($voiture)) {
            $this->voitures->add($voiture);
        }

        return $this;
    }

    public function removeVoiture(Voiture $voiture): static
    {
        $this->voitures->removeElement($voiture);

        return $this;
    }
}
