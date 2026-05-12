<?php

declare(strict_types=1);

namespace Module\Store\Action\Query;

use Module\Shared\Dto\JoinDqlDto;
use Module\Store\Entity\Store;

class JoinStoreByIdDqlQry
{
    public function joinById(string $foreignSelector, ?string $alias = null): JoinDqlDto
    {
        if (empty($alias)) {
            $alias = basename(strtr(Store::class, '\\', '/'));
        }

        return JoinDqlDto::new(
            Store::class,
            $alias,
            $foreignSelector . ' = ' . $alias . '.id',
            ['id' => 'int', 'name' => 'string'],
            __CLASS__
        );
    }
}
