<?php

declare(strict_types=1);

namespace Module\Store\Action\Dto;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class ListResponseDto
{
    public function __construct(
        /** @var StoreShortDto[] */
        #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: StoreShortDto::class)))]
        public readonly array $items,
    ) {
    }
}
