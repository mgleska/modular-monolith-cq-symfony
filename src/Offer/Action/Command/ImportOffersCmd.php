<?php

declare(strict_types=1);

namespace Module\Offer\Action\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Module\Offer\Model\Offer;
use Module\Offer\Model\OfferRepository;
use Module\Product\Action\Query\GetProductByEansQry;
use Module\Shared\Exception\AppValidationException;
use Module\Store\Action\Query\GetStoreByExternalIdQry;
use Symfony\Component\Uid\Uuid;

/** @codeCoverageIgnore */
class ImportOffersCmd
{
    public function __construct(
        private readonly GetStoreByExternalIdQry $getStoreByExternalId,
        private readonly GetProductByEansQry $getProductByEansQry,
        private readonly EntityManagerInterface $em,
        private readonly OfferRepository $repository,
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function handle(string $storeExternalId): void
    {
        $store = $this->getStoreByExternalId->handle($storeExternalId);

        if ($store === null) {
            throw new AppValidationException('storeExternalId', 'Can not find store w externalID=' . $storeExternalId);
        }

        // Fake import from external API
        $apiData = $this->generateFakeData();

        $col = new ArrayCollection($apiData);

        $eans = $col
            ->map(function (array $item) {return $item['product_ean']; })
            ->toArray();

        $this->repository->beginTransaction();
        try {
            $builder = $this->repository->createQueryBuilder('o');
            $query = $builder
                ->delete(Offer::class, 'o')
                ->where('o.storeId = :storeId')
                ->andWhere('o.productEan NOT IN (:eans)')
                ->setParameter('storeId', $store->id)
                ->setParameter('eans', $eans, ArrayParameterType::STRING)
                ->getQuery();
            $query->execute();

            $query = $this->em->createQuery('SELECT o FROM ' . Offer::class . ' o INDEX BY o.productEan WHERE o.storeId = :storeId')
                ->setParameter('storeId', $store->id);
            $dbAll = $query->getResult();

            $eanOfferMap = [];

            foreach ($col as $item) {
                if (! array_key_exists($item['product_ean'], $dbAll)) {
                    $offer = new Offer()
                        ->setStoreId($store->id)
                        ->setExternalId($item['external_id'])
                        ->setProductEan($item['product_ean'])
                        ->setProductName($item['product_name'])
                        ->setPrice($item['price'])
                        ->setLowestPrice($item['lowest_price']);
                    $this->em->persist($offer);
                    $eanOfferMap[$item['product_ean']] = $offer;
                    continue;
                }

                /** @var Offer $offer */
                $offer = $dbAll[$item['product_ean']];
                if ($this->notEqual($item, $offer)) {
                    $offer
                        ->setExternalId($item['external_id'])
                        ->setProductEan($item['product_ean'])
                        ->setProductName($item['product_name'])
                        ->setPrice($item['price'])
                        ->setLowestPrice($item['lowest_price']);
                    $this->em->persist($offer);
                    $eanOfferMap[$item['product_ean']] = $offer;
                }
            }

            $products = $this->getProductByEansQry->handle(array_keys($eanOfferMap));
            foreach ($products as $dto) {
                $eanOfferMap[$dto->ean]->setProductId($dto->id);
            }

            $this->repository->flush();

            $this->repository->commit();
        }
        catch (\Throwable $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    /**
     * @return list<array<string, string|int|null>>
     */
    private function generateFakeData(): array
    {
        $template = [
            ['product_ean' => 'ean-1', 'product_name' => 'Red square imported', 'price' => 100, 'lowest_price' => null],
            ['product_ean' => 'ean-2', 'product_name' => 'Blue square imported', 'price' => 200, 'lowest_price' => null],
            ['product_ean' => 'ean-3', 'product_name' => 'Green square imported', 'price' => 300, 'lowest_price' => null],
            ['product_ean' => 'ean-4', 'product_name' => 'Red triangle imported', 'price' => 400, 'lowest_price' => null],
            ['product_ean' => 'ean-5', 'product_name' => 'Blue triangle imported', 'price' => 500, 'lowest_price' => null],
            ['product_ean' => 'ean-6', 'product_name' => 'Green triangle imported', 'price' => 600, 'lowest_price' => null],
            ['product_ean' => 'ean-7', 'product_name' => 'Red circle imported', 'price' => 700, 'lowest_price' => null],
            ['product_ean' => 'ean-8', 'product_name' => 'Blue circle imported', 'price' => 800, 'lowest_price' => null],
            ['product_ean' => 'ean-9', 'product_name' => 'Green circle imported', 'price' => 900, 'lowest_price' => null],
        ];

        $apiData = [];
        foreach ($template as $item) {
            if ($this->skipRandom(5)) {
                continue;
            }
            $item['external_id'] = Uuid::v4()->toString();
            if ($this->skipRandom(50)) {
                $item['product_name'] = null;
            }
            $price = $item['price'];
            $item['price'] = (int)round(0.9 * $price + (0.2 * $price) * (rand(0, 100) / 100));
            if ($this->skipRandom(80)) {
                $item['lowest_price'] = (int)round($item['price'] * 1.1);
            }
            $apiData[] = $item;
        }

        return $apiData;
    }

    private function skipRandom(int $percent): bool
    {
        return rand(1, 100) <= $percent;
    }

    /**
     * @param array<string, string|int> $item
     */
    private function notEqual(array $item, Offer $offer): bool
    {
        if ($item['external_id'] !== $offer->getExternalId()
            || $item['product_ean'] !== $offer->getProductEan()
            || $item['product_name'] !== $offer->getProductName()
            || $item['price'] !== $offer->getPrice()
            || $item['lowest_price'] !== $offer->getlowestPrice()
        ) {
            return true;
        }

        return false;
    }
}
