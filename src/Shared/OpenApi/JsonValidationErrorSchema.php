<?php
declare(strict_types=1);

namespace Module\Shared\OpenApi;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class JsonValidationErrorSchema
{
    public string $type = 'https://symfony.com/errors/validation';
    public string $title = 'Validation Failed';
    public int $status = 422;
    #[OA\Property(example: 'id: This value should be of type int.')]
    public string $detail;
    /** @var JsonValidationViolationsSchema[] */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: JsonValidationViolationsSchema::class)))]
    public array $violations;
}
