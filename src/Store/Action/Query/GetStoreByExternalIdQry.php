<?php

declare(strict_types=1);

namespace Module\Store\Action\Query;

use Module\Store\Action\Dto\StoreShortDto;
use Module\Store\Model\StoreRepository;

class GetStoreByExternalIdQry
{
    public function __construct(
        private readonly StoreRepository $repository,
    ) {
    }

    public function handle(string $extId): ?StoreShortDto
    {
        $store = $this->repository->findOneBy(['externalId' => $extId]);

        if ($store === null) {
            return null;
        }

        return new StoreShortDto(
            id: $store->getId(),
            externalId: $store->getExternalId(),
            name: $store->getName(),
        );
    }
}
