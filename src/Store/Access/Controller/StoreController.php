<?php
declare(strict_types=1);

namespace Module\Store\Access\Controller;

use Module\Shared\Attribute\Group;
use Module\Shared\Attribute\JsonValidationErrorDoc;
use Module\Shared\Attribute\ResponseDoc;
use Module\Shared\Attribute\UnauthorizedErrorDoc;
use Module\Store\Action\Dto\ListResponseDto;
use Module\Store\Action\Dto\StoreDto;
use Module\Store\Action\Query\GetStoreDetailsQry;
use Module\Store\Action\Query\GetStoreListQry;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Group('mobile API')]
class StoreController extends AbstractController
{
    #[ResponseDoc(ListResponseDto::class, 200, 'Show list of stores.')]
    #[UnauthorizedErrorDoc]
    public function list(GetStoreListQry $action): JsonResponse
    {
        return new JsonResponse($action->handle());
    }

    #[ResponseDoc(StoreDto::class, 200, 'Show details of specific store.')]
    #[UnauthorizedErrorDoc, JsonValidationErrorDoc]
    public function details(#[OA\PathParameter] int $id, GetStoreDetailsQry $action): JsonResponse
    {
        return new JsonResponse($action->handle($id));
    }
}
