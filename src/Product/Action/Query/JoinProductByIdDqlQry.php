<?php

declare(strict_types=1);

namespace Module\Product\Action\Query;

use Module\Product\Model\Product;
use Module\Shared\Dto\JoinDqlDto;

class JoinProductByIdDqlQry
{
    public function joinById(string $foreignSelector, ?string $alias = null): JoinDqlDto
    {
        if (empty($alias)) {
            $alias = basename(strtr(Product::class, '\\', '/'));
        }

        return JoinDqlDto::new(
            Product::class,
            $alias,
            $foreignSelector . ' = ' . $alias . '.id',
            ['id' => 'int', 'ean' => 'string', 'name' => 'string', 'imageUrl' => 'string'],
            __CLASS__
        );
    }
}
