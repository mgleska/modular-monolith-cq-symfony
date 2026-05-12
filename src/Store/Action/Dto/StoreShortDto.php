<?php

declare(strict_types=1);

namespace Module\Store\Action\Dto;

use OpenApi\Attributes as OA;

class StoreShortDto
{
    public function __construct(
        #[OA\Property(example: 5)]
        public readonly int $id,
        #[OA\Property(example: 'r002')]
        public readonly string $externalId,
        #[OA\Property(example: 'Luboń')]
        public readonly string $name,
    ) {
    }
}
