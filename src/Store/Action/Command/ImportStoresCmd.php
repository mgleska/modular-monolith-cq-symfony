<?php
declare(strict_types=1);

namespace Module\Store\Action\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Module\Store\Model\Store;

/** @codeCoverageIgnore */
class ImportStoresCmd
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function handle(): void
    {
        // Fake import from external API
        $apiData = [
            ['rid' => 'r001', 'name' => 'Poznań', 'address' => 'ul. Bałtycka 1'],
            ['rid' => 'r002', 'name' => 'Luboń', 'address' => 'ul. Poznańska 1'],
            ['rid' => 'r003', 'name' => 'Czerwonak', 'address' => 'ul. Poznańska 2'],
            ['rid' => 'r004', 'name' => 'Kórnik', 'address' => 'ul. Poznańska 1'],
            ['rid' => 'r005', 'name' => 'Mosina', 'address' => 'ul. Poznańska 1'],
        ];

        $col = new ArrayCollection($apiData);
        $ids = $col
            ->map(function (array $item) {return $item['rid']; })
            ->toArray();

        $this->em->beginTransaction();

        try {
            $this->em->getFilters()->disable('softdeleteable');
            $query = $this->em->createQuery('SELECT s FROM ' . Store::class . ' s INDEX BY s.externalId');
            $dbAll = $query->getResult();

            foreach ($col as $item) {
                if (! array_key_exists($item['rid'], $dbAll)) {
                    $store = new Store()
                        ->setExternalId($item['rid'])
                        ->setName($item['name'])
                        ->setAddress($item['address']);
                    $this->em->persist($store);
                    continue;
                }

                /** @var Store $store */
                $store = $dbAll[$item['rid']];
                if ($store->isDeleted() || $this->notEqual($item, $store)) {
                    $store
                        ->setDeletedAt()
                        ->setExternalId($item['rid'])
                        ->setName($item['name'])
                        ->setAddress($item['address']);
                    $this->em->persist($store);
                }
            }
            $this->em->flush();

            $query = $this->em->createQuery(
                'UPDATE ' . Store::class . ' s SET s.deletedAt = CURRENT_TIMESTAMP() WHERE s.deletedAt IS NULL AND s.externalId NOT IN (:ids)'
            )
                ->setParameter('ids', $ids, ArrayParameterType::STRING);
            $query->execute();

            $this->em->commit();
        }
        catch (\Throwable $e) {
            $this->em->rollback();
            throw $e;
        }
        finally {
            $this->em->getFilters()->isEnabled('softdeleteable') || $this->em->getFilters()->enable('softdeleteable');
        }
    }

    /**
     * @param array<string, string> $item
     */
    private function notEqual(array $item, Store $store): bool
    {
        if ($item['rid'] !== $store->getExternalId()
            || $item['name'] !== $store->getName()
            || $item['address'] !== $store->getAddress()
        ) {
            return true;
        }

        return false;
    }
}
