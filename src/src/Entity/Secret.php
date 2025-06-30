<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SecretRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Represents a secret stored in the database
 * Each secret has a unique hash for retrieval, view limits and an optional expiration date
 */
#[ORM\Entity(repositoryClass: SecretRepository::class)]
#[ORM\Table(name: 'secrets')]
class Secret
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;
  
  #[ORM\Column(type: Types::STRING, length: 255, unique: true, nullable: false)]
  #[Groups(['secret:read'])]
  private string $hash;
  
  #[ORM\Column(type: Types::TEXT, nullable: false)]
  #[Groups(['secret:read'])]
  private string $secretText;
  
  #[ORM\Column(nullable: false)]
  #[Groups(['secret:read'])]
  private \DateTimeImmutable $createdAt;
  
  #[ORM\Column(nullable: true)]
  #[Groups(['secret:read'])]
  private ?\DateTimeImmutable $expiresAt = null;
  
  #[ORM\Column(nullable: false)]
  #[Groups(['secret:read'])]
  private int $remainingViews;
  
  public function __construct()
  {
    $this->createdAt = new \DateTimeImmutable();
  }
  
  /**
   * @return int|null
   */
  public function getId(): ?int
  {
    return $this->id;
  }
  
  /**
   * @return string
   */
  public function getHash(): string
  {
    return $this->hash;
  }
  
  /**
   * @param string $hash
   * @return $this
   */
  public function setHash(string $hash): self
  {
    $this->hash = $hash;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getSecretText(): string
  {
    return $this->secretText;
  }
  
  /**
   * @param string $secretText
   * @return $this
   */
  public function setSecretText(string $secretText): self
  {
    $this->secretText = $secretText;
    return $this;
  }
  
  /**
   * @return \DateTimeImmutable
   */
  public function getCreatedAt(): \DateTimeImmutable
  {
    return $this->createdAt;
  }
  
  /**
   * @param \DateTimeImmutable $createdAt
   * @return $this
   */
  public function setCreatedAt(\DateTimeImmutable $createdAt): self
  {
    $this->createdAt = $createdAt;
    return $this;
  }
  
  /**
   * @return \DateTimeImmutable|null
   */
  public function getExpiresAt(): ?\DateTimeImmutable
  {
    return $this->expiresAt;
  }
  
  /**
   * @param \DateTimeImmutable|null $expiresAt
   * @return $this
   */
  public function setExpiresAt(?\DateTimeImmutable $expiresAt): self
  {
    $this->expiresAt = $expiresAt;
    return $this;
  }
  
  /**
   * @return int
   */
  public function getRemainingViews(): int
  {
    return $this->remainingViews;
  }
  
  /**
   * @param int $remainingViews
   * @return $this
   */
  public function setRemainingViews(int $remainingViews): self
  {
    $this->remainingViews = $remainingViews;
    return $this;
  }
  
  /**
   * Decrements the number of available views
   */
  public function decrementViews(): void
  {
    if ($this->remainingViews > 0) {
      $this->remainingViews--;
    }
  }
  
  /**
   * Checks if the secret is still valid for viewing
   *
   * @return bool True if the secret is valid, false otherwise
   */
  public function isValid(): bool
  {
    $isExpired = $this->expiresAt !== null && $this->expiresAt < new \DateTimeImmutable();
    $hasNoViews = $this->remainingViews <= 0;
    
    return !$isExpired && !$hasNoViews;
  }
}
