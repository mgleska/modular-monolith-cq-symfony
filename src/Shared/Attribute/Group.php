<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */
declare(strict_types=1);

namespace Module\Shared\Attribute;

use OpenApi\Attributes as OA;
use OpenApi\Attributes\ExternalDocumentation;
use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Group extends OA\Tag
{
    public function __construct(
        ?string $name = null,
        ?string $description = Generator::UNDEFINED,
        ?string $summary = Generator::UNDEFINED,
        ?string $parent = null,
        ?string $kind = null,
        ?ExternalDocumentation $externalDocs = null,
        ?array $x = null,
        ?array $attachables = null,
    ) {
        parent::__construct($name, $description, $summary, $parent, $kind, $externalDocs, $x, $attachables);
    }
}
