<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */
declare(strict_types=1);

namespace Module\Shared;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * @template T of object
 */
trait RepositoryTrait
{
    /**
     * @param T $entity
     */
    public function save(object $entity, bool $flush = false): void
    {
        parent::getEntityManager()->persist($entity);
        if ($flush) {
            parent::getEntityManager()->flush();
        }
    }

    /**
     * @param T $entity
     */
    public function saveWithId(object $entity, bool $flush = false): void
    {
        $metadata = parent::getEntityManager()->getClassMetadata(get_class($entity));
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        parent::getEntityManager()->persist($entity);
        if ($flush) {
            parent::getEntityManager()->flush();
        }
    }

    public function flush(): void
    {
        parent::getEntityManager()->flush();
    }

    public function beginTransaction(): void
    {
        parent::getEntityManager()->beginTransaction();
    }

    public function commit(): void
    {
        parent::getEntityManager()->commit();
    }

    public function rollback(): void
    {
        parent::getEntityManager()->rollback();
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return parent::getEntityManager();
    }
}
