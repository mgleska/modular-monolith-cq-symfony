<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace Tests\Customer\Action\Command;

use Module\Customer\Action\Command\LoginCmd;
use Module\Customer\Action\Dto\LoginRequestDto;
use Module\Customer\Action\Enum\CustomerStatusEnum;
use Module\Customer\Model\Customer;
use Module\Customer\Model\CustomerRepository;
use Module\Customer\Support\Security\TokenService;
use Module\Shared\Exception\AppValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\IntegrationTestCase;

class LoginCmdTest extends IntegrationTestCase
{
    private LoginCmd $sut;
    private CustomerRepository $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->container->get(CustomerRepository::class);
        $tokenService = $this->createStub(TokenService::class);
        $tokenService->method('newAccessToken')->willReturn('JWT token');

        $this->sut = new LoginCmd(
            $this->repository,
            $tokenService,
        );
        $this->prepareDb();
    }

    #[Test, DataProvider('dataProviderHandle')]
    public function handle(
        int $customerId,
        string $expectedExceptionMessage,
    ): void {
        if ($expectedExceptionMessage) {
            $this->expectException(AppValidationException::class);
            $this->expectExceptionMessageMatches('/^' . preg_quote($expectedExceptionMessage, '/') . '$/');
        }

        $this->assertEquals(3, $this->repository->count());

        $result = $this->sut->handle(new LoginRequestDto($customerId));

        $this->assertEquals('JWT token', $result->token);

        if ($customerId > 0) {
            $customer = $this->repository->find($customerId);
            $this->assertEquals(CustomerStatusEnum::ACTIVE, $customer->getStatus());
            $this->assertEquals(3, $this->repository->count());
        }
        else {
            $this->assertEquals(4, $this->repository->count());
        }
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function dataProviderHandle(): array
    {
        return [
            'not found' => [
                'customerId' => 22,
                'expectedExceptionMessage' => 'customerId: Customer 22 not found.',
            ],
            'active' => [
                'customerId' => 5,
                'expectedExceptionMessage' => '',
            ],
            'inactive' => [
                'customerId' => 6,
                'expectedExceptionMessage' => 'customerId: Customer 6 is inactive.',
            ],
            'deactivating' => [
                'customerId' => 7,
                'expectedExceptionMessage' => '',
            ],
            'new customer' => [
                'customerId' => 0,
                'expectedExceptionMessage' => '',
            ],
        ];
    }

    private function prepareDb(): void
    {
        $this->repository->saveWithId(Customer::factory('cust 5', 2, CustomerStatusEnum::ACTIVE, 5));
        $this->repository->saveWithId(Customer::factory('cust 6', 2, CustomerStatusEnum::INACTIVE, 6));
        $this->repository->saveWithId(Customer::factory('cust 7', 2, CustomerStatusEnum::DEACTIVATING, 7));
        $this->repository->flush();
    }
}
