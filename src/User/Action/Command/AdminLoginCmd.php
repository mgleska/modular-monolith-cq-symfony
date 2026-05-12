<?php

declare(strict_types=1);

namespace Module\User\Action\Command;

use Module\User\Action\Dto\AdminLoginRequestDto;
use Module\User\Action\Dto\AdminTokenResponseDto;
use Module\User\Entity\User;
use Module\User\Support\TokenService;
use Module\User\Support\UserRepository;
use Random\RandomException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminLoginCmd
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly TokenService $tokenService,
    ) {
    }

    /**
     * @throws RandomException
     */
    public function handle(AdminLoginRequestDto $dto): AdminTokenResponseDto
    {
        $user = $this->userRepository->findOneBy(['email' => $dto->email]);
        if ($user) {
            return new AdminTokenResponseDto($user->getToken());
        }

        $user = new User();
        $plaintextPassword = 'aaaa';
        $user->setEmail($dto->email)
            ->setName($dto->email)
            ->setPassword($this->passwordHasher->hashPassword($user, $plaintextPassword))
            ->setRoles(['ROLE_USER'])
        ;

        $this->userRepository->save($user, true);

        $user->setToken($this->tokenService->generateToken($user->getId()));
        $this->userRepository->save($user, true);

        return new AdminTokenResponseDto($user->getToken());
    }
}
