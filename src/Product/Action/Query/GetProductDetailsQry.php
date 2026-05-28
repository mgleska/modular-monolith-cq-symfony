<?php

declare(strict_types=1);

namespace Module\Product\Action\Query;

use Module\Product\Action\Dto\ProductDetailsDto;
use Module\Product\Model\ProductQuantityRepository;
use Module\Product\Model\ProductRepository;
use Module\Shared\Exception\AppNeverException;

class GetProductDetailsQry
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly ProductQuantityRepository $quantityRepository,
    ) {
    }

    public function handle(int $productId, int $storeId): ProductDetailsDto
    {
        $product = $this->productRepository->find($productId);
        if ($product === null) {
            throw new AppNeverException('Product not found; productId = ' . $productId);
        }

        $quantity = $this->quantityRepository->findOneBy(['productId' => $productId, 'storeId' => $storeId]);

        return new ProductDetailsDto(
            id: $product->getId(),
            ean: $product->getEan(),
            name: $product->getName(),
            imageUrl: $product->getImageUrl(),
            quantity: $quantity?->getQuantity(),
        );
    }
}
