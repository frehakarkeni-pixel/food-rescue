<?php
namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
#[ORM\Table(name: 'offers')]
class Offer
{
    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_SOLD_OUT = 'SOLD_OUT';
    public const STATUS_EXPIRED = 'EXPIRED';
    public const STATUS_CANCELLED = 'CANCELLED';

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'commerce_id', nullable: false, onDelete: 'CASCADE')]
    private ?Commerce $commerce = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'category_id', nullable: false)]
    #[Assert\NotNull(message: 'Veuillez choisir une catégorie.')]
    private ?Category $category = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: 'Le nom du produit est obligatoire.')]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    private ?string $description = null;

    #[ORM\Column(name: 'quantity_initial')]
    #[Assert\NotNull(message: 'La quantité est obligatoire.')]
    #[Assert\Positive(message: 'La quantité doit être supérieure à 0.')]
    private ?int $quantityInitial = null;

    #[ORM\Column(name: 'quantity_available')]
    private int $quantityAvailable = 0;

    #[ORM\Column(name: 'original_price', type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotNull(message: 'Le prix initial est obligatoire.')]
    #[Assert\Positive(message: 'Le prix initial doit être supérieur à 0.')]
    private ?string $originalPrice = null;

    #[ORM\Column(name: 'discounted_price', type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotNull(message: 'Le prix réduit est obligatoire.')]
    #[Assert\PositiveOrZero(message: 'Le prix réduit ne peut pas être négatif.')]
    private ?string $discountedPrice = null;

    #[ORM\Column(name: 'available_date', type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull(message: 'La date de disponibilité est obligatoire.')]
    private ?\DateTimeImmutable $availableDate = null;

    #[ORM\Column(name: 'start_time', type: Types::TIME_IMMUTABLE)]
    #[Assert\NotNull(message: 'L\'heure de début est obligatoire.')]
    private ?\DateTimeImmutable $startTime = null;

    #[ORM\Column(name: 'pickup_deadline', type: Types::TIME_IMMUTABLE)]
    #[Assert\NotNull(message: 'L\'heure limite est obligatoire.')]
    private ?\DateTimeImmutable $pickupDeadline = null;

    #[ORM\Column(name: 'expiry_date', type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $expiryDate = null;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_ACTIVE;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = $this->updatedAt = new \DateTimeImmutable();
    }

    #[Assert\Callback]
    public function validateConsistency(ExecutionContextInterface $ctx): void
    {
        if ($this->originalPrice !== null && $this->discountedPrice !== null
            && (float) $this->discountedPrice >= (float) $this->originalPrice) {
            $ctx->buildViolation('Le prix réduit doit être inférieur au prix initial.')
                ->atPath('discountedPrice')->addViolation();
        }
        if ($this->startTime && $this->pickupDeadline
            && $this->pickupDeadline->format('H:i') <= $this->startTime->format('H:i')) {
            $ctx->buildViolation('L\'heure limite doit être après l\'heure de début.')
                ->atPath('pickupDeadline')->addViolation();
        }
        if ($this->expiryDate && $this->availableDate && $this->expiryDate < $this->availableDate) {
            $ctx->buildViolation('La date d\'expiration ne peut pas précéder la date de disponibilité.')
                ->atPath('expiryDate')->addViolation();
        }
    }

    private static function money(float|int|string|null $v): ?string
    {
        return ($v === null || $v === '') ? null : number_format((float) $v, 2, '.', '');
    }

    public function getDiscountPercent(): int
    {
        $o = (float) $this->originalPrice;
        return $o > 0 ? (int) round((1 - (float) $this->discountedPrice / $o) * 100) : 0;
    }

    public function getId(): ?int { return $this->id; }
    public function getCommerce(): ?Commerce { return $this->commerce; }
    public function setCommerce(Commerce $v): static { $this->commerce = $v; return $this; }
    public function getCategory(): ?Category { return $this->category; }
    public function setCategory(?Category $v): static { $this->category = $v; return $this; }
    public function getName(): ?string { return $this->name; }
    public function setName(?string $v): static { $this->name = $v; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $v): static { $this->description = $v; return $this; }
    public function getQuantityInitial(): ?int { return $this->quantityInitial; }
    public function setQuantityInitial(?int $v): static { $this->quantityInitial = $v; return $this; }
    public function getQuantityAvailable(): int { return $this->quantityAvailable; }
    public function setQuantityAvailable(int $v): static { $this->quantityAvailable = $v; return $this; }
    public function getOriginalPrice(): ?string { return $this->originalPrice; }
    public function setOriginalPrice(float|int|string|null $v): static { $this->originalPrice = self::money($v); return $this; }
    public function getDiscountedPrice(): ?string { return $this->discountedPrice; }
    public function setDiscountedPrice(float|int|string|null $v): static { $this->discountedPrice = self::money($v); return $this; }
    public function getAvailableDate(): ?\DateTimeImmutable { return $this->availableDate; }
    public function setAvailableDate(?\DateTimeImmutable $v): static { $this->availableDate = $v; return $this; }
    public function getStartTime(): ?\DateTimeImmutable { return $this->startTime; }
    public function setStartTime(?\DateTimeImmutable $v): static { $this->startTime = $v; return $this; }
    public function getPickupDeadline(): ?\DateTimeImmutable { return $this->pickupDeadline; }
    public function setPickupDeadline(?\DateTimeImmutable $v): static { $this->pickupDeadline = $v; return $this; }
    public function getExpiryDate(): ?\DateTimeImmutable { return $this->expiryDate; }
    public function setExpiryDate(?\DateTimeImmutable $v): static { $this->expiryDate = $v; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $v): static { $this->status = $v; return $this; }
}