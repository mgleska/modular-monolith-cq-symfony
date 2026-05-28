<?php
/** @noinspection PhpUnusedParameterInspection */
declare(strict_types=1);

namespace Tests\Customer\Access\Controller;

use Module\Customer\Action\Command\ChangeStoreCmd;
use Module\Customer\Action\Command\LoginCmd;
use Module\Customer\Support\CustomerBag;
use Module\Customer\Support\Security\TokenService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpKernel\Log\Logger;
use Tests\ApplicationTestCase;

class CustomerControllerTest extends ApplicationTestCase
{
    /**
     * @param class-string         $action
     * @param array<string, mixed> $dto
     */
    #[Test, DataProvider('dataProviderEndpoints')]
    public function blockAccessWithoutValidToken(
        string $method,
        string $path,
        string $action,
        array $dto,
        int $expected,
    ): void {
        $mock = $this->createStub($action);
        $this->client->getContainer()->set($action, $mock);
        $this->client->getContainer()->set('logger', new Logger('error', '/dev/null'));

        $this->client->jsonRequest($method, '/api' . $path, $dto);

        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @param class-string         $action
     * @param array<string, mixed> $dto
     */
    #[Test, DataProvider('dataProviderEndpoints')]
    public function blockAccessWithInvalidToken(
        string $method,
        string $path,
        string $action,
        array $dto,
        int $expected,
    ): void {
        $mock = $this->createStub($action);
        $this->client->getContainer()->set($action, $mock);
        $this->client->getContainer()->set('logger', new Logger('error', '/dev/null'));

        $this->client->jsonRequest($method, '/api' . $path, $dto, ['HTTP_AUTHORIZATION' => 'Bearer invalid_token']);

        $this->assertResponseStatusCodeSame(401);
    }

    /**
     * @param class-string         $action
     * @param array<string, mixed> $dto
     */
    #[Test, DataProvider('dataProviderEndpoints')]
    public function allowWithValidToken(
        string $method,
        string $path,
        string $action,
        array $dto,
        int $expected,
    ): void {
        $token = $this->client->getContainer()->get(TokenService::class)->newAccessToken(1, 10);
        $mock = $this->createStub($action);
        $this->client->getContainer()->set($action, $mock);

        $this->client->jsonRequest($method, '/api' . $path, $dto, ['HTTP_AUTHORIZATION' => 'Bearer ' . $token]);

        $this->assertResponseStatusCodeSame($expected);
        $bag = $this->client->getContainer()->get(CustomerBag::class);
        $this->assertInstanceOf(CustomerBag::class, $bag);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function dataProviderEndpoints(): array
    {
        return [
            [
                'method' => 'post',
                'path' => '/customer/change-store',
                'action' => ChangeStoreCmd::class,
                'dto' => ['storeId' => 20],
                'expected' => 200,
            ],
        ];
    }

    /**
     * @param class-string         $action
     * @param array<string, mixed> $dto
     */
    #[Test, DataProvider('dataProviderUnauthenticatedEndpoints')]
    public function unauthenticated(
        string $method,
        string $path,
        string $action,
        array $dto,
        int $expected,
    ): void {
        $mock = $this->createStub($action);
        $this->client->getContainer()->set($action, $mock);

        $this->client->jsonRequest($method, '/api' . $path, $dto);

        $this->assertResponseStatusCodeSame($expected);
        $bag = $this->client->getContainer()->get(CustomerBag::class);
        $this->assertInstanceOf(CustomerBag::class, $bag);
        $this->assertNull($bag->customerId);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function dataProviderUnauthenticatedEndpoints(): array
    {
        return [
            [
                'method' => 'post',
                'path' => '/customer/login',
                'action' => LoginCmd::class,
                'dto' => ['customerId' => 0],
                'expected' => 200,
            ],
        ];
    }
}
