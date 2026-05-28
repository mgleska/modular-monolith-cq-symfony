<?php

declare(strict_types=1);

namespace Module\Store\Action\Query;

use Doctrine\ORM\EntityManagerInterface;
use Module\Store\Action\Dto\StoreShortDto;
use Module\Store\Model\Store;

class GetStoreListQry
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return StoreShortDto[]
     */
    public function handle(): array
    {
        $query = $this->entityManager->createQuery(
            'SELECT NEW ' . StoreShortDto::class . '(s.id, s.externalId, s.name) FROM ' . Store::class . ' s ORDER BY s.name ASC'
        );

        return $query->getResult();
    }
}
