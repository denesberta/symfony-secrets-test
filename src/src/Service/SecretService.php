<?php

namespace App\Service;

use App\Entity\Secret;
use App\Repository\SecretRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Handles the core secret server business logic
 */
class SecretService
{
  private EntityManagerInterface $entityManager;
  private SecretRepository $secretRepository;
  
  public function __construct(EntityManagerInterface $entityManager, SecretRepository $secretRepository)
  {
    $this->entityManager = $entityManager;
    $this->secretRepository = $secretRepository;
  }
  
  /**
   * Creates a new secret and persists it to the database
   *
   * @param string $secretText The content of the secret
   * @param int $expireAfterViews The number of times the secret can be viewed
   * @param int $expireAfterMinutes The TTL in minutes
   * @return Secret The newly created secret entity
   * @throws \Exception
   */
  public function createSecret(string $secretText, int $expireAfterViews, int $expireAfterMinutes): Secret
  {
    $secret = new Secret();
    $secret->setSecretText($secretText);
    $secret->setRemainingViews($expireAfterViews);
    
    // Generate a unique hash
    $secret->setHash(hash('sha256', uniqid(microtime(), true)));
    
    // Set expiration time if provided else never expires
    if ($expireAfterMinutes > 0) {
      $expiresAt = (new \DateTimeImmutable())->add(new \DateInterval("PT{$expireAfterMinutes}M"));
      $secret->setExpiresAt($expiresAt);
    }
    
    $this->entityManager->persist($secret);
    $this->entityManager->flush();
    
    return $secret;
  }
  
  /**
   * Retrieves a secret by its hash, handling expiration and view count logic
   *
   * @param string $hash The hash of the secret to retrieve
   * @return Secret|null The secret if it's valid, or null if it's not found, expired, or has no views left
   */
  public function getValidSecretByHash(string $hash): ?Secret
  {
    $secret = $this->secretRepository->findOneByHash($hash);
    
    // Return null if no secret is found
    if (!$secret) {
      return null;
    }
    
    if (!$secret->isValid()) {
      // Clean up by deleting the invalid secret from the database
      $this->entityManager->remove($secret);
      $this->entityManager->flush();
      return null;
    }
    
    // The secret is valid, so decrement its view count before returning
    $secret->decrementViews();
    $this->entityManager->flush();
    
    return $secret;
  }
}
