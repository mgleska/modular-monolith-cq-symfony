<?php
declare(strict_types=1);

namespace Module\Shared\OpenApi;

use OpenApi\Attributes as OA;

class JsonValidationViolationsSchema
{
    #[OA\Property(example: 'id')]
    public string $propertyPath;
    #[OA\Property(example: 'This value should be of type int.')]
    public string $title;
}
