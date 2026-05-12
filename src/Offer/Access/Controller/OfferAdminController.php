<?php

declare(strict_types=1);

namespace Module\Offer\Access\Controller;

use Module\Offer\Action\Command\ChangeVisibilityCmd;
use Module\Offer\Action\Dto\Admin\ChangeVisibilityDto;
use Module\Offer\Action\Dto\Admin\FiltersResponseDto;
use Module\Offer\Action\Dto\Admin\ListParamDto;
use Module\Offer\Action\Dto\Admin\ListResponseDto;
use Module\Offer\Action\Dto\Admin\OfferDto;
use Module\Offer\Action\Query\AdminGetListFiltersQry;
use Module\Offer\Action\Query\AdminGetOfferDetailsQry;
use Module\Offer\Action\Query\AdminGetOfferListQry;
use Module\Shared\Attribute\Group;
use Module\Shared\Attribute\JsonValidationErrorDoc;
use Module\Shared\Attribute\ResponseDoc;
use Module\Shared\Attribute\UnauthorizedErrorDoc;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[Group('CMS')]
class OfferAdminController extends AbstractController
{
    #[ResponseDoc(FiltersResponseDto::class, 200, 'Filters for offer list')]
    #[UnauthorizedErrorDoc, JsonValidationErrorDoc]
    public function filters(AdminGetListFiltersQry $action): JsonResponse
    {
        return new JsonResponse($action->handle());
    }

    #[ResponseDoc(ListResponseDto::class, 200, 'List offers. Show list of offers. With selection by store or product name.')]
    #[UnauthorizedErrorDoc, JsonValidationErrorDoc]
    public function list(#[MapQueryString] ListParamDto $dto, AdminGetOfferListQry $action): JsonResponse
    {
        return new JsonResponse($action->handle($dto));
    }

    #[ResponseDoc(OfferDto::class, 200, 'Show offer. Show details of specific offer.')]
    #[UnauthorizedErrorDoc, JsonValidationErrorDoc]
    public function details(#[OA\PathParameter] int $id, AdminGetOfferDetailsQry $action): JsonResponse
    {
        return new JsonResponse($action->handle($id));
    }

    /**
     * @throws \Throwable
     */
    #[ResponseDoc('', 200, 'Change visibility. The endpoint allows to change the visibility of offer.')]
    #[UnauthorizedErrorDoc, JsonValidationErrorDoc]
    public function changeVisibility(#[MapRequestPayload] ChangeVisibilityDto $dto, ChangeVisibilityCmd $action): JsonResponse
    {
        $action->handle($dto);

        return new JsonResponse('');
    }
}
