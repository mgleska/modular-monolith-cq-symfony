<?php

declare(strict_types=1);

namespace Module\Customer\Action\Command;

use Module\Customer\Action\Dto\LoginRequestDto;
use Module\Customer\Action\Dto\TokenResponseDto;
use Module\Customer\Action\Enum\CustomerStatusEnum;
use Module\Customer\Entity\Customer;
use Module\Customer\Support\CustomerRepository;
use Module\Customer\Support\Security\TokenService;
use Module\Shared\Exception\AppValidationException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class LoginCmd
{
    public function __construct(
        private readonly CustomerRepository $repository,
        private readonly TokenService $tokenService,
    ) {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(LoginRequestDto $dto): TokenResponseDto
    {
        if (0 === $dto->customerId) {
            $customer = new Customer();
            $customer->setName('Customer');
            $this->repository->save($customer, true);
            $customer->setName('Customer ' . $customer->getId());
            $this->repository->save($customer, true);
        }
        else {
            $customer = $this->repository->find($dto->customerId);
            if (null === $customer) {
                throw new AppValidationException('customerId', 'Customer ' . $dto->customerId . ' not found.');
            }
        }

        if (CustomerStatusEnum::INACTIVE === $customer->getStatus()) {
            throw new AppValidationException('customerId', 'Customer ' . $dto->customerId . ' is inactive.');
        }

        if (CustomerStatusEnum::DEACTIVATING === $customer->getStatus()) {
            $customer->setStatus(CustomerStatusEnum::ACTIVE);
            $this->repository->save($customer, true);
        }

        $jwt = $this->tokenService->newAccessToken($customer->getId(), $customer->getSelectedStore());

        return new TokenResponseDto($jwt);
    }
}
