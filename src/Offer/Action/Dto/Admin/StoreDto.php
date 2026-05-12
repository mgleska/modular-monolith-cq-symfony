<?php
declare(strict_types=1);

namespace Module\Offer\Action\Dto\Admin;

use OpenApi\Attributes as OA;

class StoreDto
{
    public function __construct(
        #[OA\Property(example: 5)]
        public readonly int $storeId,
        #[OA\Property(example: 'Luboń')]
        public readonly string $storeName,
    ) {
    }
}
