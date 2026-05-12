<?php

declare(strict_types=1);

namespace Module\Offer\Action\Dto\Admin;

use OpenApi\Attributes as OA;

class OfferDto
{
    public function __construct(
        #[OA\Property(example: 45)]
        public readonly int $id,
        #[OA\Property(example: 1)]
        public readonly int $version,
        #[OA\Property(example: true)]
        public readonly bool $visible,
        #[OA\Property(example: '8410564006257')]
        public readonly string $productEan,
        #[OA\Property(example: 'Red square')]
        public readonly string $productName,
        #[OA\Property(description: 'amount in 0,01 of 1 PLN', example: 805)]
        public readonly int $price,
        #[OA\Property(description: 'amount in 0,01 of 1 PLN', example: 950)]
        public readonly ?int $lowestPrice,
        #[OA\Property(example: 'https://my.images.com/red-square.png')]
        public readonly string $imageUrl,
        #[OA\Property(example: 7000)]
        public readonly ?int $quantity,
    ) {
    }
}
