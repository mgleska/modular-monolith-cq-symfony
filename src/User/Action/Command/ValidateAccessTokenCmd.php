<?php

declare(strict_types=1);

namespace Module\User\Action\Command;

use Module\User\Support\TokenService;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class ValidateAccessTokenCmd implements AccessTokenHandlerInterface
{
    public function __construct(
        private readonly TokenService $tokenService,
    ) {
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        return new UserBadge($this->tokenService->validateToken($accessToken));
    }
}
