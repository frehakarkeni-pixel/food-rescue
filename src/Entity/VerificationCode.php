<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'verification_codes')]
class VerificationCode
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'user_id', nullable: false, onDelete: 'CASCADE')]
    private User $user;
    #[ORM\Column(length: 10)]
    private string $channel;
    #[ORM\Column(name: 'code_hash', length: 255)]
    private string $codeHash;
    #[ORM\Column(name: 'expires_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $expiresAt;
    #[ORM\Column(name: 'used_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $usedAt = null;
    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(User $user, string $channel, string $codeHash, \DateTimeImmutable $expiresAt)
    {
        $this->user = $user; $this->channel = $channel;
        $this->codeHash = $codeHash; $this->expiresAt = $expiresAt;
        $this->createdAt = new \DateTimeImmutable();
    }
    public function getUser(): User { return $this->user; }
    public function getCodeHash(): string { return $this->codeHash; }
    public function getExpiresAt(): \DateTimeImmutable { return $this->expiresAt; }
    public function getUsedAt(): ?\DateTimeImmutable { return $this->usedAt; }
    public function markUsed(): void { $this->usedAt = new \DateTimeImmutable(); }
}