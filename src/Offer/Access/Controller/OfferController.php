<?php
declare(strict_types=1);

namespace Module\Offer\Access\Controller;

use Module\Offer\Action\Dto\ListParamDto;
use Module\Offer\Action\Dto\ListResponseDto;
use Module\Offer\Action\Dto\OfferDto;
use Module\Offer\Action\Query\GetOfferDetailsQry;
use Module\Offer\Action\Query\GetOfferListQry;
use Module\Shared\Attribute\Group;
use Module\Shared\Attribute\JsonValidationErrorDoc;
use Module\Shared\Attribute\ResponseDoc;
use Module\Shared\Attribute\UnauthorizedErrorDoc;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;

#[Group('mobile API')]
class OfferController extends AbstractController
{
    #[ResponseDoc(ListResponseDto::class, 200, 'List offers. Show list of offers in selected store.')]
    #[UnauthorizedErrorDoc, JsonValidationErrorDoc]
    public function list(#[MapQueryString] ListParamDto $dto, GetOfferListQry $action): JsonResponse
    {
        return new JsonResponse($action->handle($dto));
    }

    #[ResponseDoc(OfferDto::class, 200, 'Show offer. Show details of specific offer.')]
    #[UnauthorizedErrorDoc, JsonValidationErrorDoc]
    public function details(#[OA\PathParameter] int $id, GetOfferDetailsQry $action): JsonResponse
    {
        return new JsonResponse($action->handle($id));
    }
}
