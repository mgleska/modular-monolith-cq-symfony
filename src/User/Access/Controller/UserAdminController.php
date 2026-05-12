<?php

declare(strict_types=1);

namespace Module\User\Access\Controller;

use Module\Shared\Attribute\Group;
use Module\Shared\Attribute\JsonValidationErrorDoc;
use Module\Shared\Attribute\ResponseDoc;
use Module\User\Action\Command\AdminLoginCmd;
use Module\User\Action\Dto\AdminLoginRequestDto;
use Module\User\Action\Dto\AdminTokenResponseDto;
use Nelmio\ApiDocBundle\Attribute\Security;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[Group('CMS')]
class UserAdminController extends AbstractController
{
    /**
     * @throws RandomException
     */
    #[ResponseDoc(AdminTokenResponseDto::class, 200, 'ONLY FOR DEMO! Login CMS. Endpoint returns token for supplied CMS user (email). If given user does not exist in database - will be created.')]
    #[JsonValidationErrorDoc]
    #[Security([])]
    public function login(#[MapRequestPayload] AdminLoginRequestDto $dto, AdminLoginCmd $action): JsonResponse
    {
        return new JsonResponse($action->handle($dto));
    }
}
