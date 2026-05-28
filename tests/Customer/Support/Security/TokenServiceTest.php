<?php
/** @noinspection PhpDocMissingThrowsInspection
 *  @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Tests\Customer\Support\Security;

use Firebase\JWT\JWT;
use Module\Customer\Support\Security\TokenService;
use Module\Shared\Exception\AppNeverException;
use Module\Shared\Exception\AppUnauthorizedException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Tests\UnitTestCase;

class TokenServiceTest extends UnitTestCase
{
    private TokenService $sut;

    private MockObject|ContainerBagInterface $params;

    private const int USER_ID = 10;

    private const int STORE_ID = 2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->params = $this->createStub(ContainerBagInterface::class);

        $this->sut = new TokenService(
            $this->params,
        );
    }

    #[Test]
    public function tokenStructure(): void
    {
        $this->params->method('get')->willReturnMap([
            ['kernel.secret', 'TESTSECRETTESTSECRETTESTSECRETTESTSECRETTESTSECRET'],
            ['mm-cq.access-token-ttl', 28800],
        ]);

        $token = $this->sut->newAccessToken(self::USER_ID, self::STORE_ID);

        $this->assertNotEmpty($token);
        $parts = explode('.', $token);
        $this->assertCount(3, $parts);
        $this->assertEquals('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9', $parts[0]);
    }

    #[Test]
    public function encodeDecode(): void
    {
        $this->params->method('get')->willReturnMap([
            ['kernel.secret', 'TESTSECRETTESTSECRETTESTSECRETTESTSECRETTESTSECRET'],
            ['mm-cq.access-token-ttl', 28800],
        ]);

        $token = $this->sut->newAccessToken(self::USER_ID, self::STORE_ID);
        $payload = $this->sut->decodeAccessToken($token);

        $this->assertNotEmpty($payload);
        $this->assertEquals('acc', $payload['sub']);
        $this->assertEquals(self::USER_ID, $payload['uid']);
        $this->assertEquals(self::STORE_ID, $payload['stid']);
        $this->assertNotEmpty($payload['exp']);
    }

    /**
     * @param array<int, array<int, string|int>> $getConfigResponse
     */
    #[Test, DataProvider('dataProviderException')]
    public function exception(
        array $getConfigResponse,
        string $token,
        string $expected,
        string $message,
    ): void {
        $this->params->method('get')->willReturnMap($getConfigResponse);

        $this->expectException($expected);
        $this->expectExceptionMessageMatches('/^' . preg_quote($message, '/') . '$/');

        $this->sut->decodeAccessToken($token);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function dataProviderException(): array
    {
        return [
            'invalid kernel.secret' => [
                'getConfigResponse' => [
                    ['kernel.secret', ''],
                ],
                'token' => '',
                'expected' => AppNeverException::class,
                'message' => 'Not found configuration parameter "kernel.secret"',
            ],
            'Wrong number of segments' => [
                'getConfigResponse' => [
                    ['kernel.secret', 'TESTSECRETTESTSECRETTESTSECRETTESTSECRETTESTSECRET'],
                    ['mm-cq.access-token-ttl', 28800],
                ],
                'token' => 'invalid token',
                'expected' => AppUnauthorizedException::class,
                'message' => 'JWT token is invalid.',
            ],
            'Malformed UTF-8 characters' => [
                'getConfigResponse' => [
                    ['kernel.secret', 'TESTSECRETTESTSECRETTESTSECRETTESTSECRETTESTSECRET'],
                    ['mm-cq.access-token-ttl', 28800],
                ],
                'token' => 'part1.part2.part3',
                'expected' => AppUnauthorizedException::class,
                'message' => 'JWT token is invalid.',
            ],
            'Syntax error, malformed JSON' => [
                'getConfigResponse' => [
                    ['kernel.secret', 'TESTSECRETTESTSECRETTESTSECRETTESTSECRETTESTSECRET'],
                    ['mm-cq.access-token-ttl', 28800],
                ],
                'token' => base64_encode('part1') . '.' . base64_encode('part2') . '.' . base64_encode('part3'),
                'expected' => AppUnauthorizedException::class,
                'message' => 'JWT token is invalid.',
            ],
        ];
    }

    #[Test]
    public function expiredToken(): void
    {
        $this->params->method('get')->willReturnMap([
            ['kernel.secret', 'TESTSECRETTESTSECRETTESTSECRETTESTSECRETTESTSECRET'],
            ['mm-cq.access-token-ttl', -10],
        ]);

        $this->expectException(AppUnauthorizedException::class);
        $this->expectExceptionMessageMatches('/^JWT token is invalid[.]$/');

        $token = $this->sut->newAccessToken(self::USER_ID, self::STORE_ID);
        $this->sut->decodeAccessToken($token);
    }

    #[Test]
    public function invalidPayload(): void
    {
        $this->params->method('get')->willReturnMap([
            ['kernel.secret', 'TESTSECRETTESTSECRETTESTSECRETTESTSECRETTESTSECRET'],
        ]);

        $payload = [
            'exp' => new \DateTime()->getTimestamp() + 100,
        ];
        $token = JWT::encode($payload, 'TESTSECRETTESTSECRETTESTSECRETTESTSECRETTESTSECRET', 'HS256');

        $this->expectException(AppUnauthorizedException::class);
        $this->expectExceptionMessageMatches('/^JWT token is not access token[.]$/');

        $this->sut->decodeAccessToken($token);
    }
}
