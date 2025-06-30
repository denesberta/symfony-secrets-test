<?php

namespace App\Repository;

use App\Entity\Secret;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Secret>
 *
 * @method Secret|null find($id, $lockMode = null, $lockVersion = null)
 * @method Secret|null findOneBy(array $criteria, array $orderBy = null)
 * @method Secret[]    findAll()
 * @method Secret[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SecretRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Secret::class);
  }
  
  /**
   * Finds a single secret by its unique hash
   *
   * @param string $hash The hash to search for
   * @return Secret|null The found secret
   */
  public function findOneByHash(string $hash): ?Secret
  {
    return $this->findOneBy(['hash' => $hash]);
  }
}
