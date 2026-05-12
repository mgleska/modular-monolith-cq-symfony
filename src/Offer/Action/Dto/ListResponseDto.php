<?php

declare(strict_types=1);

namespace Module\Offer\Action\Dto;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class ListResponseDto
{
    public function __construct(
        /** @var OfferShortDto[] */
        #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: OfferShortDto::class)))]
        public readonly array $items,
        #[OA\Property(example: 1)]
        public readonly int $page,
        #[OA\Property(example: 5)]
        public readonly int $perPage,
    ) {
    }
}
