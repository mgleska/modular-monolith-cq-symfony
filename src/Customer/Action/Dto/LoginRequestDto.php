<?php

declare(strict_types=1);

namespace Module\Customer\Action\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class LoginRequestDto
{
    public function __construct(
        #[Assert\PositiveOrZero]
        public readonly int $customerId,
    ) {
    }
}
