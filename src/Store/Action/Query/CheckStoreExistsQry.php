<?php

declare(strict_types=1);

namespace Module\Store\Action\Query;

use Module\Store\Support\StoreRepository;

class CheckStoreExistsQry
{
    public function __construct(
        private readonly StoreRepository $repository,
    ) {
    }

    public function check(int $id): bool
    {
        $store = $this->repository->find($id);

        return $store !== null;
    }
}
