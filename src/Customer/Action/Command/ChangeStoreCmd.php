<?php
declare(strict_types=1);

namespace Module\Customer\Action\Command;

use Module\Customer\Action\Dto\ChangeStoreRequestDto;
use Module\Customer\Action\Dto\TokenResponseDto;
use Module\Customer\Support\CustomerBag;
use Module\Customer\Support\CustomerRepository;
use Module\Customer\Support\Security\TokenService;
use Module\Shared\Exception\AppNeverException;
use Module\Shared\Exception\AppValidationException;
use Module\Store\Action\Query\CheckStoreExistsQry;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ChangeStoreCmd
{
    public function __construct(
        private readonly CheckStoreExistsQry $checkStoreExistsQry,
        private readonly TokenService $tokenService,
        private readonly CustomerBag $customerBag,
        private readonly CustomerRepository $repository,
    ) {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(ChangeStoreRequestDto $dto): TokenResponseDto
    {
        if (!$this->checkStoreExistsQry->check($dto->storeId)) {
            throw new AppValidationException('storeId', 'Invalid store id');
        }

        if ($this->customerBag->customerId === null) {
            throw new AppNeverException('Customer is not authenticated.');
        }

        $customer = $this->repository->find($this->customerBag->customerId);
        if (!$customer) {
            throw new NotFoundHttpException('Customer not found.');
        }
        $customer->setSelectedStore($dto->storeId);
        $this->repository->save($customer, true);

        $jwt = $this->tokenService->newAccessToken($this->customerBag->customerId, $dto->storeId);

        return new TokenResponseDto(token: $jwt);
    }
}
