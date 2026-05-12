<?php

declare(strict_types=1);

namespace Module\Product\Action\Dto;

class ProductShortDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $ean,
    ) {
    }
}
