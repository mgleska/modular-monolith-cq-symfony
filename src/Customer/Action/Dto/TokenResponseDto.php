<?php

declare(strict_types=1);

namespace Module\Customer\Action\Dto;

use OpenApi\Attributes as OA;

class TokenResponseDto
{
    public function __construct(
        #[OA\Property(example: 'JWT access_token')]
        public readonly string $token,
    ) {
    }
}
