<?php

declare(strict_types=1);

namespace Module\User\Support;

use Random\RandomException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class TokenService
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    /**
     * @throws RandomException
     */
    public function generateToken(int $userId): string
    {
        return $userId . '-' . bin2hex(random_bytes(32));
    }

    public function validateToken(string $token): string
    {
        $part = explode('-', $token);
        if (count($part) !== 2) {
            throw new BadCredentialsException('Invalid token format.');
        }

        $user = $this->userRepository->find((int)$part[0]);

        if ($user === null || $user->getToken() !== $token) {
            throw new BadCredentialsException('Invalid token.');
        }

        return $user->getEmail();
    }
}
