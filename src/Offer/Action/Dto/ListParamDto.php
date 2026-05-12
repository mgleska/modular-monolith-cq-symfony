<?php

declare(strict_types=1);

namespace Module\Offer\Action\Dto;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class ListParamDto
{
    public function __construct(
        #[Assert\Positive, OA\Property(example: 2)]
        public readonly ?int $page,
    ) {
    }
}
