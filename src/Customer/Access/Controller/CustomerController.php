<?php

declare(strict_types=1);

namespace Module\Customer\Access\Controller;

use Module\Customer\Action\Command\ChangeStoreCmd;
use Module\Customer\Action\Command\LoginCmd;
use Module\Customer\Action\Dto\ChangeStoreRequestDto;
use Module\Customer\Action\Dto\LoginRequestDto;
use Module\Customer\Action\Dto\TokenResponseDto;
use Module\Shared\Attribute\Group;
use Module\Shared\Attribute\JsonValidationErrorDoc;
use Module\Shared\Attribute\ResponseDoc;
use Module\Shared\Attribute\UnauthorizedErrorDoc;
use Nelmio\ApiDocBundle\Attribute\Security;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[Group('mobile API')]
class CustomerController extends AbstractController
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[ResponseDoc(TokenResponseDto::class, 200, 'ONLY FOR DEMO! Login. Endpoint returns new JWT access token for supplied mobile user ID. If given user does not exist in database - will be created.')]
    #[Security([])]
    #[JsonValidationErrorDoc]
    public function login(#[MapRequestPayload] LoginRequestDto $dto, LoginCmd $action): JsonResponse
    {
        return new JsonResponse($action->handle($dto));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[ResponseDoc(TokenResponseDto::class, 200, 'Set store for mobile user. Endpoint sets given store for mobile user and returns new JWT access token with this store.')]
    #[UnauthorizedErrorDoc, JsonValidationErrorDoc]
    public function changeStore(#[MapRequestPayload] ChangeStoreRequestDto $dto, ChangeStoreCmd $action): JsonResponse
    {
        return new JsonResponse($action->handle($dto));
    }
}
