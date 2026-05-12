<?php

declare(strict_types=1);

namespace Module\Offer\Action\Dto\Admin;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class FiltersResponseDto
{
    public function __construct(
        /** @var StoreDto[] */
        #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: StoreDto::class)))]
        public readonly array $stores,
    ) {
    }
}
