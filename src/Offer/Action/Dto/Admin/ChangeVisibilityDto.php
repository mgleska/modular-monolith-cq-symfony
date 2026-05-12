<?php

declare(strict_types=1);

namespace Module\Offer\Action\Dto\Admin;

use OpenApi\Attributes as OA;

class ChangeVisibilityDto
{
    public function __construct(
        #[OA\Property(example: 45)]
        public readonly int $id,
        #[OA\Property(example: 2)]
        public readonly int $version,
        #[OA\Property(example: false)]
        public readonly bool $visible,
    ) {
    }
}
