<?php

declare(strict_types=1);

namespace Module\Offer\Action\Dto\Admin;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class ListParamDto
{
    public function __construct(
        public readonly ?string $search,
        #[Assert\Positive, OA\Property(example: 5)]
        public readonly ?int $storeId,
        #[Assert\Positive, OA\Property(example: 1)]
        public readonly ?int $page,
        #[Assert\Positive, OA\Property(example: 10)]
        public readonly ?int $perPage,
    ) {
    }
}
