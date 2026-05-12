<?php

declare(strict_types=1);

namespace Module\Shared\Dto;

use Doctrine\ORM\Query\Expr;
use Module\Shared\Exception\AppNeverException;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class JoinDqlDto
{
    private function __construct(
        public readonly string $dqlStatement,
        /** @var array{string, string, string, Expr\Comparison|Expr\Composite|Expr\Func|string|null} */
        public readonly array $builderStatement,
        /** @var array<string, string> */
        public readonly array $providesColumns,
        public readonly string $alias,
        public readonly string $builderName,
    ) {
    }

    /**
     * @param class-string          $entityClass
     * @param array<string, string> $providesColumns
     */
    public static function new(
        string $entityClass,
        string $alias,
        string $condition,
        array $providesColumns,
        string $builderName,
    ): self {
        if (empty($alias)) {
            throw new AppNeverException('Entity alias cannot be empty');
        }

        return new self(
            $entityClass . ' AS ' . $alias . ' ON ' . $condition,
            [$entityClass, $alias, Expr\Join::ON, $condition],
            $providesColumns,
            $alias,
            $builderName
        );
    }

    /**
     * @param array<string, string> $required
     */
    public function confirmRequiredColumns(array $required): void
    {
        $diff = array_diff_assoc($required, $this->providesColumns);

        if (\count($diff) > 0) {
            throw new AppNeverException("SQL column list supplied by builder $this->builderName do not contain all required columns. Missing columns: " . \implode(', ', \array_keys($diff)));
        }
    }
}
