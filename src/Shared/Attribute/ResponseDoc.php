<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */
declare(strict_types=1);

namespace Module\Shared\Attribute;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_FUNCTION | \Attribute::TARGET_METHOD)]
class ResponseDoc extends OA\Response
{
    public function __construct(
        string $dtoClass,
        int $status = 200,
        ?string $description = null,
    ) {
        parent::__construct(response: $status, description: $description, content: $dtoClass ? new Model(type: $dtoClass) : null);
    }
}
