<?php

namespace App\Tests\Service;

use Exception;
use App\Entity\Secret;
use App\Service\SecretService;
use PHPUnit\Framework\TestCase;
use App\Repository\SecretRepository;
use Doctrine\ORM\EntityManagerInterface;

class SecretServiceTest extends TestCase
{
  private SecretService $secretService;
  
  private $entityManager;
  private $secretRepository;
  
  /**
   * @return void
   * @throws \PHPUnit\Framework\MockObject\Exception
   */
  protected function setUp(): void
  {
    $this->entityManager = $this->createMock(EntityManagerInterface::class);
    $this->secretRepository = $this->createMock(SecretRepository::class);
    
    $this->secretService = new SecretService($this->entityManager, $this->secretRepository);
  }
  
  /**
   * @return void
   * @throws Exception
   */
  public function testCreateSecretSuccessfully(): void
  {
    $secret = $this->secretService->createSecret('my-secret-text', 5, 10);
    
    $this->assertInstanceOf(Secret::class, $secret);
    $this->assertEquals('my-secret-text', $secret->getSecretText());
    $this->assertEquals(5, $secret->getRemainingViews());
    $this->assertNotNull($secret->getExpiresAt());
  }
  
  /**
   * @return void
   * @throws Exception
   */
  public function testCreateSecretWithZeroExpiration(): void
  {
    $secret = $this->secretService->createSecret('my-secret-text', 1, 0);
    $this->assertNull($secret->getExpiresAt());
  }
  
  public function testGetValidSecretByHashDecrementsViews(): void
  {
    $secret = new Secret();
    $secret->setSecretText('test');
    $secret->setRemainingViews(3);
    
    $this->secretRepository->expects($this->once())
      ->method('findOneByHash')
      ->with('some-hash')
      ->willReturn($secret);
    
    // Expect flush to be called to save the decremented view count
    $this->entityManager->expects($this->once())->method('flush');
    
    $result = $this->secretService->getValidSecretByHash('some-hash');
    
    $this->assertSame($secret, $result);
    $this->assertEquals(2, $result->getRemainingViews());
  }
  
  public function testGetSecretReturnsNullIfNotFound(): void
  {
    $result = $this->secretService->getValidSecretByHash('non-existent-hash');
    
    $this->assertNull($result);
  }
  
  public function testGetSecretReturnsNullAndRemovesIfExpiredByViews(): void
  {
    $secret = new Secret();
    $secret->setRemainingViews(0); // No views left
    
    $this->secretRepository->expects($this->once())
      ->method('findOneByHash')
      ->with('expired-hash')
      ->willReturn($secret);
    
    $result = $this->secretService->getValidSecretByHash('expired-hash');
    
    $this->assertNull($result);
  }
  
  /**
   * @throws \DateInvalidOperationException
   */
  public function testGetSecretReturnsNullAndRemovesIfExpiredByTime(): void
  {
    $secret = new Secret();
    $secret->setRemainingViews(5);
    // Set expiration to a time in the past
    $secret->setExpiresAt((new \DateTimeImmutable())->sub(new \DateInterval('PT1H')));
    
    $this->secretRepository->expects($this->once())
      ->method('findOneByHash')
      ->with('expired-hash')
      ->willReturn($secret);
    
    $result = $this->secretService->getValidSecretByHash('expired-hash');
    
    $this->assertNull($result);
  }
}
