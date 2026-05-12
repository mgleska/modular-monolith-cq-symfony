<?php

declare(strict_types=1);

namespace Module\Customer\Support;

class CustomerBag
{
    public function __construct(
        public ?int $customerId = null,
        public int $selectedStoreId = 0,
    ) {
    }
}
