<?php

declare(strict_types=1);

namespace Module\Product\Action\Query;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Module\Product\Action\Dto\ProductShortDto;
use Module\Product\Entity\Product;

class GetProductByEansQry
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @param string[] $eans
     *
     * @return ProductShortDto[]
     */
    public function handle(array $eans): array
    {
        if (empty($eans)) {
            return [];
        }

        $query = $this->em->createQuery(
            'SELECT NEW ' . ProductShortDto::class . '(p.id, p.ean) FROM ' . Product::class . ' p WHERE p.ean IN (:eans)'
        )
            ->setParameter('eans', $eans, ArrayParameterType::STRING);

        return $query->getResult();
    }
}
