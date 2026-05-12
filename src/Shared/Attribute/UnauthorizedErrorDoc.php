<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */
declare(strict_types=1);

namespace Module\Shared\Attribute;

use Module\Shared\OpenApi\UnauthorizedErrorSchema;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_FUNCTION | \Attribute::TARGET_METHOD)]
class UnauthorizedErrorDoc extends OA\Response
{
    public function __construct()
    {
        parent::__construct(
            response: 401,
            description: 'Unauthorized',
            content: new Model(type: UnauthorizedErrorSchema::class),
        );
    }
}
