<?php
declare(strict_types=1);

namespace Module\Product\Support;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Module\Product\Entity\ProductQuantity;

/**
 * @extends ServiceEntityRepository<ProductQuantity>
 */
class ProductQuantityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductQuantity::class);
    }

    public function save(ProductQuantity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
