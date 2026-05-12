<?php

declare(strict_types=1);

namespace Module\Customer\Action\Command;

use Module\Customer\Support\CustomerBag;
use Module\Customer\Support\Security\TokenService;
use Module\Shared\Exception\AppNeverException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class ValidateAccessTokenCmd implements AccessTokenHandlerInterface
{
    public function __construct(
        private readonly TokenService $tokenService,
        private readonly CustomerBag $customerBag,
    ) {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        $payload = $this->tokenService->decodeAccessToken($accessToken);

        if (!isset($payload['uid'])) {
            throw new AppNeverException('Missing "uid" in JWT token');
        }

        $this->customerBag->customerId = $payload['uid'];
        $this->customerBag->selectedStoreId = $payload['stid'] ?? 0;

        return new UserBadge((string)$payload['uid']);
    }
}
