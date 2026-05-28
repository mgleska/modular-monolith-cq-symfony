<?php

declare(strict_types=1);

namespace Module\Customer\Support\Security;

use Module\Customer\Action\Enum\CustomerStatusEnum;
use Module\Customer\Model\Customer;
use Module\Customer\Support\CustomerBag;
use Module\Shared\Exception\AppUnauthorizedException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @implements UserProviderInterface<Customer>
 */
class CustomerProvider implements UserProviderInterface
{
    public function __construct(
        private readonly CustomerBag $customerBag,
    ) {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        if (null === $this->customerBag->customerId) {
            throw new AppUnauthorizedException('Customer is not authenticated.');
        }

        if ($this->customerBag->customerId !== (int)$identifier) {
            throw new AppUnauthorizedException('Unexpected customer identifier.');
        }

        return Customer::factory(
            name: '',
            selectedStore: $this->customerBag->selectedStoreId,
            status: CustomerStatusEnum::ACTIVE,
            id: $this->customerBag->customerId
        );
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (! $user instanceof Customer) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', $user::class));
        }

        // Return a User object after making sure its data is "fresh".
        // Or throw a UserNotFoundException if the user no longer exists.
        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return Customer::class === $class || \is_subclass_of($class, Customer::class);
    }
}
