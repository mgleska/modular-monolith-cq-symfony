<?php

declare(strict_types=1);

namespace Module\Offer\Action\Dto\Admin;

use OpenApi\Attributes as OA;

class OfferShortDto
{
    public function __construct(
        #[OA\Property(example: 45)]
        public readonly int $id,
        #[OA\Property(example: 'Luboń')]
        public readonly string $storeName,
        #[OA\Property(example: true)]
        public readonly bool $visible,
        #[OA\Property(example: '8410564006257')]
        public readonly string $productEan,
        #[OA\Property(example: 'Red square')]
        public readonly string $productName,
        #[OA\Property(description: 'amount in 0,01 of 1 PLN', example: 805)]
        public readonly int $price,
    ) {
    }
}
