<?php

declare(strict_types=1);

namespace Module\User\Action\Dto;

class AdminTokenResponseDto
{
    public function __construct(
        public readonly string $token,
    ) {
    }
}
