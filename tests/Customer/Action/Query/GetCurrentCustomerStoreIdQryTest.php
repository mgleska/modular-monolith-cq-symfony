<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace Tests\Customer\Action\Query;

use Module\Customer\Action\Query\GetCurrentCustomerStoreIdQry;
use Module\Customer\Support\CustomerBag;
use Module\Shared\Exception\AppNeverException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\UnitTestCase;

class GetCurrentCustomerStoreIdQryTest extends UnitTestCase
{
    private GetCurrentCustomerStoreIdQry $sut;

    private CustomerBag $customerBag;

    public function setUp(): void
    {
        parent::setUp();

        $this->customerBag = new CustomerBag();

        $this->sut = new GetCurrentCustomerStoreIdQry(
            $this->customerBag,
        );
    }

    /**
     * @param array<string, int>|null $bagData
     */
    #[Test, DataProvider('dataProviderHandle')]
    public function handle(
        ?array $bagData,
        int $expected,
        bool $expectedException,
    ): void {
        if ($bagData) {
            $this->customerBag->customerId = $bagData['customerId'];
            $this->customerBag->selectedStoreId = $bagData['selectedStoreId'];
        }

        if ($expectedException) {
            $this->expectException(AppNeverException::class);
            $this->expectExceptionMessageMatches('/^Customer is not authenticated[.]$/');
        }

        $result = $this->sut->handle();

        $this->assertSame($expected, $result);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function dataProviderHandle(): array
    {
        return [
            'success' => [
                'bagData' => ['customerId' => 1, 'selectedStoreId' => 5],
                'expected' => 5,
                'expectedException' => false,
            ],
            'exception' => [
                'bagData' => null,
                'expected' => 0,
                'expectedException' => true,
            ],
        ];
    }
}
