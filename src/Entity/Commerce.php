<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'commerces')]
class Commerce
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;
    #[ORM\OneToOne]
    #[ORM\JoinColumn(name: 'user_id', nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;
    #[ORM\Column(name: 'business_name', length: 150)]
    private ?string $businessName = null;
    #[ORM\Column(name: 'business_type', length: 20)]
    private ?string $businessType = null;
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;
    #[ORM\Column(length: 255)]
    private ?string $address = null;
    #[ORM\Column(length: 100)]
    private ?string $city = null;
    #[ORM\Column(name: 'postal_code', length: 20, nullable: true)]
    private ?string $postalCode = null;
    #[ORM\Column(name: 'opening_hours', type: 'json', nullable: true)]
    private ?array $openingHours = null;
    #[ORM\Column(name: 'verification_status', length: 20)]
    private string $verificationStatus = 'PENDING';
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;
    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct() { $this->createdAt = $this->updatedAt = new \DateTimeImmutable(); }

    public function getId(): ?int { return $this->id; }
    public function setUser(User $u): static { $this->user = $u; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setBusinessName(string $v): static { $this->businessName = $v; return $this; }
    public function getBusinessName(): ?string { return $this->businessName; }
    public function setBusinessType(string $v): static { $this->businessType = $v; return $this; }
    public function getBusinessType(): ?string { return $this->businessType; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $v): static { $this->description = $v; return $this; }
    public function setAddress(string $v): static { $this->address = $v; return $this; }
    public function getAddress(): ?string { return $this->address; }
    public function setCity(string $v): static { $this->city = $v; return $this; }
    public function getCity(): ?string { return $this->city; }
    public function getPostalCode(): ?string { return $this->postalCode; }
    public function setPostalCode(?string $v): static { $this->postalCode = $v; return $this; }
    public function getOpeningHours(): ?array { return $this->openingHours; }
    public function setOpeningHours(?array $v): static { $this->openingHours = $v ?: null; return $this; }
    public function getVerificationStatus(): string { return $this->verificationStatus; }
    public function isApproved(): bool { return $this->verificationStatus === 'APPROVED'; }
}