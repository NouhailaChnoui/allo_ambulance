<?php
namespace App\Entity;

use App\Repository\DemandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeRepository::class)]
class Demande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'demandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $client = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $adresseDepart = null;

    #[ORM\Column(length: 255)]
    private ?string $adresseDestination = null;

    #[ORM\Column(nullable: true)]
    private ?int $nombrePatients = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = null;

    #[ORM\ManyToOne]
    private ?Ambulance $ambulance = null;

    #[ORM\ManyToOne]
    private ?Driver $driver = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->statut = 'en_attente';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): static
    {
        $this->client = $client;
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

    public function getAdresseDepart(): ?string
    {
        return $this->adresseDepart;
    }

    public function setAdresseDepart(string $adresseDepart): static
    {
        $this->adresseDepart = $adresseDepart;
        return $this;
    }

    public function getAdresseDestination(): ?string
    {
        return $this->adresseDestination;
    }

    public function setAdresseDestination(string $adresseDestination): static
    {
        $this->adresseDestination = $adresseDestination;
        return $this;
    }

    public function getNombrePatients(): ?int
    {
        return $this->nombrePatients;
    }

    public function setNombrePatients(?int $nombrePatients): static
    {
        $this->nombrePatients = $nombrePatients;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getAmbulance(): ?Ambulance
    {
        return $this->ambulance;
    }

    public function setAmbulance(?Ambulance $ambulance): static
    {
        $this->ambulance = $ambulance;
        return $this;
    }

    public function getDriver(): ?Driver
    {
        return $this->driver;
    }

    public function setDriver(?Driver $driver): static
    {
        $this->driver = $driver;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    public function getTypeLabel(): string
    {
        $types = [
            'urgence' => 'Urgence',
            'muti-urgent' => 'Multi-Urgent',
            'transport' => 'Transport'
        ];
        return $types[$this->type] ?? $this->type;
    }

    public function getStatutLabel(): string
    {
        $statuts = [
            'en_attente' => 'En attente',
            'en_cours' => 'En cours',
            'termine' => 'Terminé'
        ];
        return $statuts[$this->statut] ?? $this->statut;
    }
}
