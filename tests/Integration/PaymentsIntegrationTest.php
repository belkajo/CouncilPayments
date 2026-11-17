<?php

use D3R\Db;
use Local\CouncilPayments\Interfaces\Db\SearchItem as SearchItemInterface;
use Local\CouncilPayments\Model\Collection\PaymentCollection;
use Local\CouncilPayments\Model\Db\Filter;
use Local\CouncilPayments\Model\Db\Pagination;
use Local\CouncilPayments\Model\Db\SearchItem;
use Local\CouncilPayments\Model\Payments;
use PHPUnit\Framework\TestCase;

final class PaymentsIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        if (getenv('D3R_INTEGRATION') !== '1') {
            self::markTestSkipped('Set D3R_INTEGRATION=1 to run database integration tests.');
        }

        require_once __DIR__ . '/../../../../../config/database.php';
    }

    public function testSearchFiltersSupplierAndRating(): void
    {
        $row = Db::get()->selectFirst('SELECT payment_supplier, payment_cost_rating FROM payments ORDER BY payment_id LIMIT 1');
        self::assertIsArray($row, 'The supplied payments table must contain data.');

        $filter = new Filter();
        $filter->add((new SearchItem())->setFieldName('payment_supplier')->setValue($row['payment_supplier'])->setOperation(SearchItemInterface::OPERATION_LIKE));
        $filter->add((new SearchItem())->setFieldName('payment_cost_rating')->setValue((int) $row['payment_cost_rating'])->setOperation(SearchItemInterface::OPERATION_EQ));
        $model = new Payments();
        $payments = $model->search(new PaymentCollection(), $filter, new Pagination(1, 5));

        self::assertGreaterThan(0, $model->totalRows());
        self::assertGreaterThan(0, count($payments));
        foreach ($payments as $payment) {
            self::assertStringContainsStringIgnoringCase($row['payment_supplier'], $payment->getPaymentSupplier());
            self::assertSame((int) $row['payment_cost_rating'], $payment->getPaymentCostRating());
        }
    }

    public function testPaginationReturnsDistinctRowsAndConsistentTotal(): void
    {
        $model = new Payments();
        $first = $model->search(new PaymentCollection(), null, new Pagination(1, 5));
        $total = $model->totalRows();
        self::assertGreaterThan(5, $total, 'The supplied payments table must contain more than five rows.');
        $second = $model->search(new PaymentCollection(), null, new Pagination(2, 5));

        self::assertSame($total, $model->totalRows());
        self::assertCount(5, $first);
        self::assertCount(min(5, $total - 5), $second);
        $firstIds = array_map(fn ($payment) => $payment->getPaymentId(), iterator_to_array($first));
        $secondIds = array_map(fn ($payment) => $payment->getPaymentId(), iterator_to_array($second));
        self::assertSame([], array_values(array_intersect($firstIds, $secondIds)));
    }

    public function testInjectionLikeSearchReturnsNoMatches(): void
    {
        $filter = new Filter();
        $filter->add((new SearchItem())->setFieldName('payment_supplier')->setValue("' OR 1=1 -- ")->setOperation(SearchItemInterface::OPERATION_EQ));
        $model = new Payments();
        $payments = $model->search(new PaymentCollection(), $filter, new Pagination(1, 5));

        self::assertCount(0, $payments);
        self::assertSame(0, $model->totalRows());
    }
}
