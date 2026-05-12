<?php

declare(strict_types=1);

namespace Module\Customer\Action\Query;

use Module\Customer\Support\CustomerBag;
use Module\Shared\Exception\AppNeverException;

class GetCurrentCustomerStoreIdQry
{
    public function __construct(
        private readonly CustomerBag $customerBag,
    ) {
    }

    public function handle(): int
    {
        if ($this->customerBag->customerId === null) {
            throw new AppNeverException('Customer is not authenticated.');
        }

        return $this->customerBag->selectedStoreId;
    }
}
