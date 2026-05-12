<?php
/** @noinspection MissingServiceXml */
declare(strict_types=1);

namespace Module\Customer\Support\Security;

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Module\Shared\Exception\AppUnauthorizedException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class TokenService
{
    private const string TYPE_ACCESS = 'acc';

    // private const string TYPE_REFRESH = 'rfr';

    private const string ALGORITHM = 'HS256';

    public function __construct(
        private readonly ContainerBagInterface $params,
    ) {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function newAccessToken(int $customerId, int $storeId): string
    {
        $payload = [
            'sub' => self::TYPE_ACCESS,
            'uid' => $customerId,
            'stid' => $storeId,
            'exp' => new \DateTime()->getTimestamp() + $this->params->get('mm-cq.access-token-ttl'),
        ];

        return JWT::encode($payload, $this->getKey(), self::ALGORITHM);
    }

    /**
     * @return array<string, string|int>
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function decodeAccessToken(string $jwt): array
    {
        try {
            $decoded = (array)JWT::decode($jwt, new Key($this->getKey(), self::ALGORITHM));
        }
        catch (\InvalidArgumentException|\DomainException|\UnexpectedValueException|SignatureInvalidException|BeforeValidException
                |BeforeValidException|ExpiredException) {
                    throw new AppUnauthorizedException('JWT token is invalid.');
                }
        if (($decoded['sub'] ?? '') !== self::TYPE_ACCESS) {
            throw new AppUnauthorizedException('JWT token is not access token.');
        }

        return $decoded;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getKey(): string
    {
        return $this->params->get('kernel.secret');
    }
}
