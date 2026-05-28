<?php
declare(strict_types=1);

namespace Module\Product\Model;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Module\Shared\RepositoryTrait;

/**
 * @extends ServiceEntityRepository<ProductQuantity>
 */
class ProductQuantityRepository extends ServiceEntityRepository
{
    /** @use RepositoryTrait<ProductQuantity> */
    use RepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductQuantity::class);
    }
}
