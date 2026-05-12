<?php

declare(strict_types=1);

namespace Module\Customer\Action\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ChangeStoreRequestDto
{
    public function __construct(
        #[Assert\Positive]
        public readonly int $storeId,
    ) {
    }
}
