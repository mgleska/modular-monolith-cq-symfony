<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace Tests\User\Access\Controller;

use Module\User\Access\Controller\UserAdminController;
use Module\User\Action\Command\AdminLoginCmd;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\ApplicationTestCase;

#[CoversClass(UserAdminController::class)]
class UserAdminControllerTest extends ApplicationTestCase
{
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
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function dataProviderUnauthenticatedEndpoints(): array
    {
        return [
            [
                'method' => 'post',
                'path' => '/admin/user/login',
                'action' => AdminLoginCmd::class,
                'dto' => ['email' => 'user@my.company.com'],
                'expected' => 200,
            ],
        ];
    }
}
