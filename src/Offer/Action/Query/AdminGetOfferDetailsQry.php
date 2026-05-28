<?php

declare(strict_types=1);

namespace Module\Offer\Action\Query;

use Module\Offer\Action\Dto\Admin\OfferDto;
use Module\Offer\Model\OfferRepository;
use Module\Product\Action\Query\GetProductDetailsQry;
use Module\Shared\Exception\AppValidationException;

class AdminGetOfferDetailsQry
{
    public function __construct(
        private readonly OfferRepository $repository,
        private readonly GetProductDetailsQry $getProductDetailsQry,
    ) {
    }

    public function handle(int $id): OfferDto
    {
        $offer = $this->repository->find($id);

        if ($offer === null) {
            throw new AppValidationException('offerId', 'Offer not found');
        }

        if ($offer->getProductId()) {
            $product = $this->getProductDetailsQry->handle($offer->getProductId(), $offer->getStoreId());
        }
        else {
            $product = null;
        }

        return new OfferDto(
            id: $offer->getId(),
            version: $offer->getVersion(),
            visible: $offer->isVisible(),
            productEan: $offer->getProductEan(),
            productName: $offer->getProductName() ?? $product->name ?? '',
            price: $offer->getPrice(),
            lowestPrice: $offer->getLowestPrice(),
            imageUrl: $product->imageUrl ?? '',
            quantity: $product?->quantity,
        );
    }
}
