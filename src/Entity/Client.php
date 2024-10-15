<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['devis:read','voiture:read', 'client:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Groups(['devis:read','voiture:read', 'client:read'])]
    private ?string $nom = null;

    #[ORM\Column(length: 20)]
    #[Groups(['devis:read','voiture:read', 'client:read'])]
    private ?string $prenom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['devis:read','voiture:read', 'client:read'])]
    private ?\DateTimeInterface $date_naissance = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['devis:read','voiture:read', 'client:read'])]
    private ?bool $est_personne = null;

    /**
     * @var Collection<int, Devis>
     */
    #[ORM\OneToMany(targetEntity: Devis::class, mappedBy: 'client')]
    private Collection $devis;

    /**
     * @var Collection<int, Voiture>
     */
    #[ORM\OneToMany(targetEntity: Voiture::class, mappedBy: 'client')]
    private Collection $voitures;

    public function __construct()
    {
        $this->devis = new ArrayCollection();
        $this->voitures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(\DateTimeInterface $date_naissance): static
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }

    public function isEstPersonne(): ?bool
    {
        return $this->est_personne ? 'Professionnel' : 'Particulier';
    }

    public function setEstPersonne(?bool $est_personne): static
    {
        $this->est_personne = $est_personne;

        return $this;
    }

    /**
     * @return Collection<int, Devis>
     */
    public function getDevis(): Collection
    {
        return $this->devis;
    }

    public function addDevi(Devis $devis): static
    {
        if (!$this->devis->contains($devis)) {
            $this->devis->add($devis);
            $devis->setClient($this);
        }

        return $this;
    }

    public function removeDevi(Devis $devis): static
    {
        if ($this->devis->removeElement($devis)) {
            // set the owning side to null (unless already changed)
            if ($devis->getClient() === $this) {
                $devis->setClient(null);
            }
        }

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
            $voiture->setClient($this);
        }

        return $this;
    }

    public function removeVoiture(Voiture $voiture): static
    {
        if ($this->voitures->removeElement($voiture)) {
            // set the owning side to null (unless already changed)
            if ($voiture->getClient() === $this) {
                $voiture->setClient(null);
            }
        }

        return $this;
    }
}
