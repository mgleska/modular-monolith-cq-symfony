<?php

declare(strict_types=1);

namespace Module\Offer\Action\Query;

use Doctrine\ORM\EntityManagerInterface;
use Module\Customer\Action\Query\GetCurrentCustomerStoreIdQry;
use Module\Offer\Action\Dto\ListParamDto;
use Module\Offer\Action\Dto\ListResponseDto;
use Module\Offer\Action\Dto\OfferShortDto;
use Module\Offer\Entity\Offer;
use Module\Product\Action\Query\JoinProductByIdDqlQry;

class GetOfferListQry
{
    private const int PER_PAGE = 5;

    public function __construct(
        private readonly GetCurrentCustomerStoreIdQry $getCurrentCustomerStoreIdQry,
        private readonly JoinProductByIdDqlQry $joinProductByIdDqlQry,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function handle(ListParamDto $dto): ListResponseDto
    {
        $storeId = $this->getCurrentCustomerStoreIdQry->handle();

        $joinProduct = $this->joinProductByIdDqlQry->joinById('o.productId', 'p');
        $joinProduct->confirmRequiredColumns(['id' => 'int', 'name' => 'string']);

        $query = $this->em->createQuery('
            SELECT NEW ' . OfferShortDto::class . '(o.id, o.productEan, COALESCE(o.productName, ' . $joinProduct->alias . '.name, \'\'), o.price, o.lowestPrice)
            FROM ' . Offer::class . ' o
            LEFT JOIN ' . $joinProduct->dqlStatement . '
            WHERE o.storeId = :storeId
            AND o.visible = 1
        ')
        ->setParameter('storeId', $storeId);

        $page = max((int)$dto->page, 1);
        $query
            ->setFirstResult(($page - 1) * self::PER_PAGE)
            ->setMaxResults(self::PER_PAGE);

        return new ListResponseDto($query->getResult(), $page, self::PER_PAGE);
    }
}
