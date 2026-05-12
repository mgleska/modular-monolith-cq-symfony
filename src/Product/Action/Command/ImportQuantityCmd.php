<?php

declare(strict_types=1);

namespace Module\Product\Action\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Module\Product\Entity\Product;
use Module\Product\Entity\ProductQuantity;
use Module\Product\Support\ProductRepository;
use Module\Store\Action\Query\GetStoreListQry;

class ImportQuantityCmd
{
    public function __construct(
        private readonly GetStoreListQry $getStoreListQry,
        private readonly ProductRepository $productRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function handle(): void
    {
        // Fake import from external API

        $stores = $this->getStoreListQry->handle();
        $ids = new ArrayCollection($this->productRepository->findAll())
            ->map(fn (Product $product) => $product->getId())
            ->toArray();

        $this->em->getConnection()->executeStatement('TRUNCATE ' . ProductQuantity::TABLE);

        $this->em->clear();
        foreach ($stores as $store) {
            foreach ($ids as $id) {
                $quantity = rand(0, 20);
                if ($quantity == 0) {
                    continue;
                }
                $entity = new ProductQuantity()
                    ->setStoreId($store->id)
                    ->setProductId($id)
                    ->setQuantity($quantity * 1000);
                $this->em->persist($entity);
            }
        }

        $this->em->flush();
    }
}
