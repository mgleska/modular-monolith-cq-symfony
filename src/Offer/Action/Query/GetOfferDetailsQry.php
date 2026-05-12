<?php
declare(strict_types=1);

namespace Module\Offer\Action\Query;

use Module\Customer\Action\Query\GetCurrentCustomerStoreIdQry;
use Module\Offer\Action\Dto\OfferDto;
use Module\Offer\Action\Enum\QuantityLevelEnum;
use Module\Offer\Support\OfferRepository;
use Module\Product\Action\Query\GetProductDetailsQry;
use Module\Shared\Exception\AppValidationException;

class GetOfferDetailsQry
{
    private const int QUANTITY_LEVEL = 5000;

    public function __construct(
        private readonly GetCurrentCustomerStoreIdQry $getCurrentCustomerStoreIdQry,
        private readonly OfferRepository $repository,
        private readonly GetProductDetailsQry $getProductDetailsQry,
    ) {
    }

    public function handle(int $id): OfferDto
    {
        $storeId = $this->getCurrentCustomerStoreIdQry->handle();

        $offer = $this->repository->find($id);
        if ($offer === null) {
            throw new AppValidationException('offerId', 'Offer not found');
        }

        if ($offer->getStoreId() !== $storeId) {
            throw new AppValidationException('offerId', 'Requested offer belongs to other store.');
        }
        if ($offer->isVisible() !== true) {
            throw new AppValidationException('visible', 'Requested offer is not visible.');
        }
        if ($offer->getProductId() === null) {
            throw new AppValidationException('productId', 'Requested offer is for unknown product.');
        }

        $product = $this->getProductDetailsQry->handle($offer->getProductId(), $storeId);
        $quantityLevel = match (true) {
            $product->quantity >= self::QUANTITY_LEVEL => QuantityLevelEnum::AVAILABLE,
            $product->quantity > 0 => QuantityLevelEnum::AVAILABLE_LOW,
            default => QuantityLevelEnum::UNKNOWN,
        };

        return new OfferDto(
            id: $offer->getId(),
            productEan: $offer->getProductEan(),
            productName: $offer->getProductName() ?? $product->name,
            price: $offer->getPrice(),
            lowestPrice: $offer->getLowestPrice(),
            imageUrl: $product->imageUrl,
            quantityLevel: $quantityLevel,
        );
    }
}
