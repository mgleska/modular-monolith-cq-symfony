<?php
declare(strict_types=1);

namespace Module\Store\Model;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Module\Shared\RepositoryTrait;

/**
 * @extends ServiceEntityRepository<Store>
 */
class StoreRepository extends ServiceEntityRepository
{
    /** @use RepositoryTrait<Store> */
    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Store::class);
    }
}
