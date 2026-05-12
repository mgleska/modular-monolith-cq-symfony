<?php

declare(strict_types=1);

namespace Module\Offer\Action\Query;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Module\Offer\Action\Dto\Admin\ListParamDto;
use Module\Offer\Action\Dto\Admin\ListResponseDto;
use Module\Offer\Action\Dto\Admin\OfferShortDto;
use Module\Offer\Entity\Offer;
use Module\Product\Action\Query\JoinProductByIdDqlQry;
use Module\Store\Action\Query\JoinStoreByIdDqlQry;

class AdminGetOfferListQry
{
    private const int PER_PAGE = 10;

    public function __construct(
        private readonly JoinProductByIdDqlQry $joinProductByIdDqlQry,
        private readonly JoinStoreByIdDqlQry $joinStoreByIdDqlQry,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function handle(ListParamDto $dto): ListResponseDto
    {
        $joinStore = $this->joinStoreByIdDqlQry->joinById('o.storeId');
        $joinStore->confirmRequiredColumns(['id' => 'int', 'name' => 'string']);

        $joinProduct = $this->joinProductByIdDqlQry->joinById('o.productId');
        $joinProduct->confirmRequiredColumns(['id' => 'int', 'name' => 'string']);

        $query = $this->em->createQueryBuilder();
        $query
            ->from(Offer::class, 'o')
            ->leftJoin(...$joinStore->builderStatement)
            ->leftJoin(...$joinProduct->builderStatement)
            ->select('o.id, o.visible, o.productEan, o.price, ' .
                $joinStore->alias . '.name AS storeName,' .
                'COALESCE(o.productName, ' . $joinProduct->alias . '.name, \'\') AS productName'
            )
            ->orderBy($joinStore->alias . '.name')
            ->addOrderBy($joinProduct->alias . '.name');

        if ($dto->storeId) {
            $query->andWhere('o.storeId = :storeId')
                ->setParameter('storeId', $dto->storeId);
        }

        if ($dto->search) {
            $query->andWhere(
                $query->expr()->orX(
                    'o.productEan LIKE :search',
                    'COALESCE(o.productName, ' . $joinProduct->alias . '.name) LIKE :search'
                )
            )
            ->setParameter('search', '%' . $dto->search . '%');
        }

        $page = max((int)$dto->page, 1);
        $perPage = max($dto->perPage ?? self::PER_PAGE, 1);

        $paginator = new Paginator($query, false);

        $result = new ArrayCollection($paginator
            ->getQuery()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getResult()
        )
        ->map(
            function (array $item): OfferShortDto {
                return new OfferShortDto(
                    id: $item['id'],
                    storeName: $item['storeName'],
                    visible: $item['visible'],
                    productEan: $item['productEan'],
                    productName: $item['productName'],
                    price: $item['price']
                );
            }
        )
        ->toArray();

        return new ListResponseDto($result, $page, $perPage);
    }
}
