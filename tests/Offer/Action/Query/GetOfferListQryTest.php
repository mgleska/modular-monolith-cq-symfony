<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace Tests\Offer\Action\Query;

use Module\Customer\Action\Query\GetCurrentCustomerStoreIdQry;
use Module\Offer\Action\Dto\ListParamDto;
use Module\Offer\Action\Dto\OfferShortDto;
use Module\Offer\Action\Query\GetOfferListQry;
use Module\Offer\Model\Offer;
use Module\Offer\Model\OfferRepository;
use Module\Product\Action\Query\JoinProductByIdDqlQry;
use Module\Product\Model\Product;
use Module\Product\Model\ProductRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\IntegrationTestCase;

class GetOfferListQryTest extends IntegrationTestCase
{
    private GetOfferListQry $sut;

    private OfferRepository $offerRepository;
    private ProductRepository $productRepository;

    private const int STORE_ID = 10;
    private const int STORE2_ID = 12;

    public function setUp(): void
    {
        parent::setUp();

        $this->offerRepository = $this->container->get(OfferRepository::class);
        $this->productRepository = $this->container->get(ProductRepository::class);

        $getCurrentCustomerStoreIdQry = $this->createStub(GetCurrentCustomerStoreIdQry::class);
        $getCurrentCustomerStoreIdQry->method('handle')->willReturn(self::STORE_ID);

        $this->sut = new GetOfferListQry(
            $getCurrentCustomerStoreIdQry,
            $this->container->get(JoinProductByIdDqlQry::class),
            $this->offerRepository->getEntityManager()
        );
        $this->prepareDb();
    }

    /**
     * @param OfferShortDto[] $expectedItems
     */
    #[Test, DataProvider('dataProviderHandle')]
    public function handle(
        ?int $page,
        array $expectedItems,
        int $expectedPage,
    ): void {
        $result = $this->sut->handle(new ListParamDto($page));

        $this->assertEquals($expectedItems, $result->items);
        $this->assertEquals($expectedPage, $result->page);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function dataProviderHandle(): array
    {
        return [
            'first page' => [
                'page' => 1,
                'expectedItems' => [
                    new OfferShortDto(30, 'ean-1', 'Red square', 1100, 1200),
                    new OfferShortDto(31, 'ean-2', 'Red circle basic', 1300, null),
                ],
                'expectedPage' => 1,
            ],
            'default page' => [
                'page' => null,
                'expectedItems' => [
                    new OfferShortDto(30, 'ean-1', 'Red square', 1100, 1200),
                    new OfferShortDto(31, 'ean-2', 'Red circle basic', 1300, null),
                ],
                'expectedPage' => 1,
            ],
            'second page' => [
                'page' => 2,
                'expectedItems' => [],
                'expectedPage' => 2,
            ],
        ];
    }

    private function prepareDb(): void
    {
        $this->offerRepository->saveWithId(Offer::factory(self::STORE_ID, 'EXT-30', 'ean-1', 'Red square', 1100, 1200, true, 100, 2, 30));
        $this->offerRepository->saveWithId(Offer::factory(self::STORE_ID, 'EXT-31', 'ean-2', null, 1300, null, true, 101, 3, 31));
        $this->offerRepository->saveWithId(Offer::factory(self::STORE_ID, 'EXT-32', 'ean-3', 'Red triangle', 1600, 1700, false, 102, 0, 32));
        $this->offerRepository->saveWithId(Offer::factory(self::STORE2_ID, 'EXT-33', 'ean-1', 'Red square', 1400, 1500, true, 100, 0, 33));
        $this->offerRepository->flush();

        $this->productRepository->saveWithId(Product::factory('ean-1', 'Red square basic', 'red-square.png', 100));
        $this->productRepository->saveWithId(Product::factory('ean-2', 'Red circle basic', null, 101));
        $this->productRepository->saveWithId(Product::factory('ean-3', 'Red triangle basic', null, 103));
        $this->productRepository->flush();
    }
}
