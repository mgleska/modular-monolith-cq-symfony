<?php

declare(strict_types=1);

namespace Module\Product\Action\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Module\Product\Entity\Product;

class ImportProductsCmd
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
            ['ean' => 'ean-1', 'name' => 'Red square', 'image' => 'red-square.png'],
            ['ean' => 'ean-2', 'name' => 'Blue square', 'image' => 'blue-square.png'],
            ['ean' => 'ean-3', 'name' => 'Green square', 'image' => 'green-square.png'],
            ['ean' => 'ean-4', 'name' => 'Red triangle', 'image' => 'red-triangle.png'],
            ['ean' => 'ean-5', 'name' => 'Blue triangle', 'image' => 'blue-triangle.png'],
            ['ean' => 'ean-6', 'name' => 'Green triangle', 'image' => 'green-triangle.png'],
            ['ean' => 'ean-7', 'name' => 'Red circle', 'image' => 'red-circle.png'],
            ['ean' => 'ean-8', 'name' => 'Blue circle', 'image' => 'blue-circle.png'],
            ['ean' => 'ean-9', 'name' => 'Green circle', 'image' => 'green-circle.png'],
        ];

        $col = new ArrayCollection($apiData);
        $eans = $col
            ->map(function (array $item) {return $item['ean']; })
            ->toArray();

        $this->em->beginTransaction();

        try {
            $query = $this->em->createQuery('SELECT p FROM ' . Product::class . ' p INDEX BY p.ean');
            $dbAll = $query->getResult();

            foreach ($col as $item) {
                if (!array_key_exists($item['ean'], $dbAll)) {
                    $product = new Product()
                        ->setEan($item['ean'])
                        ->setName($item['name'])
                        ->setImageUrl($item['image']);
                    $this->em->persist($product);
                    continue;
                }

                /** @var Product $product */
                $product = $dbAll[$item['ean']];
                if ($this->notEqual($item, $product)) {
                    $product
                        ->setEan($item['ean'])
                        ->setName($item['name'])
                        ->setImageUrl($item['image']);
                    $this->em->persist($product);
                }
            }
            $this->em->flush();

            $query = $this->em->createQuery(
                'DELETE FROM ' . Product::class . ' p WHERE p.ean NOT IN (:eans)'
            )
                ->setParameter('eans', $eans, ArrayParameterType::STRING);
            $query->execute();

            $this->em->commit();
        }
        catch (\Throwable $e) {
            $this->em->rollback();
            throw $e;
        }
    }

    /**
     * @param array<string, string> $item
     */
    private function notEqual(array $item, Product $product): bool
    {
        if ($item['ean'] !== $product->getEan()
            || $item['name'] !== $product->getName()
            || $item['image'] !== $product->getImageUrl()
        ) {
            return true;
        }

        return false;
    }
}
