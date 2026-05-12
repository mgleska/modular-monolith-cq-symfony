<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */
declare(strict_types=1);

namespace Module\Shared\Attribute;

use Module\Shared\OpenApi\JsonValidationErrorSchema;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_FUNCTION | \Attribute::TARGET_METHOD)]
class JsonValidationErrorDoc extends OA\Response
{
    public function __construct()
    {
        parent::__construct(
            response: 422,
            description: 'Validation Failed',
            content: new Model(type: JsonValidationErrorSchema::class),
        );
    }
}
