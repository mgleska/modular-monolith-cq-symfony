<?php

declare(strict_types=1);

namespace Module\User\Action\Dto;

class AdminLoginRequestDto
{
    public function __construct(
        public string $email,
    ) {
    }
}
