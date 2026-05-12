<?php

declare(strict_types=1);

namespace Module\Store\Action\Query;

use Module\Shared\Exception\AppValidationException;
use Module\Store\Action\Dto\StoreDto;
use Module\Store\Support\StoreRepository;

class GetStoreDetailsQry
{
    public function __construct(
        private readonly StoreRepository $repository,
    ) {
    }

    public function handle(int $id): StoreDto
    {
        $store = $this->repository->find($id);
        if ($store === null) {
            throw new AppValidationException('id', 'Store not found');
        }

        return new StoreDto(
            id: $store->getId(),
            externalId: $store->getExternalId(),
            name: $store->getName(),
            address: $store->getAddress(),
        );
    }
}
