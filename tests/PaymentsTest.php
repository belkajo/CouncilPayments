<?php

use D3R\Model;
use Local\CouncilPayments\Interfaces\Payment as PaymentInterface;
use Local\CouncilPayments\Model\Payments;
use Local\CouncilPayments\Model\Collection\PaymentCollection;
use Local\CouncilPayments\Model\Db\Filter;
use Local\CouncilPayments\Model\Db\SearchItem;
use Local\CouncilPayments\Model\Db\Pagination;
use Local\CouncilPayments\Interfaces\Db\SearchItem as SearchItemInterface;
use PHPUnit\Framework\TestCase;

final class PaymentsTest extends TestCase
{
    public function testCreatesPaymentFromDatabaseRow(): void
    {
        $record = Payments::createFromArray([
            'payment_id' => '42', 'payment_supplier' => 'Example supplier',
            'payment_ref' => '12345', 'payment_cost_rating' => '3', 'payment_amount' => '620.00',
        ]);

        self::assertInstanceOf(Model::class, $record);
        $payment = $record->toPayment();
        self::assertSame('payments', Payments::tableName());
        self::assertSame('payment_id', Payments::idColName());
        self::assertInstanceOf(PaymentInterface::class, $payment);
        self::assertSame(42, $payment->getPaymentId());
        self::assertSame('Example supplier', $payment->getPaymentSupplier());
        self::assertSame('12345', $payment->getPaymentRef());
        self::assertSame(3, $payment->getPaymentCostRating());
        self::assertSame('620.00', $payment->getPaymentAmount());
    }

    public function testSearchPassesFilterToPaginatedQuery(): void
    {
        $item = new SearchItem();
        $item->setFieldName('payment_supplier');
        $item->setValue('Example');
        $item->setOperation(SearchItemInterface::OPERATION_LIKE);
        $filter = new Filter();
        $filter->add($item);
        $collection = new PaymentCollection();

        $model = new SearchablePaymentsForTest();
        $result = $model->search($collection, $filter, new Pagination(2, 5));

        self::assertSame($collection, $result);
        self::assertSame([
            ['SELECT COUNT(*) AS payment_id, COUNT(*) AS payment_total FROM payments WHERE ' . $filter->getSql(), $filter->getParameters()],
            ['SELECT * FROM payments WHERE ' . $filter->getSql() . ' ORDER BY payment_id LIMIT 5 OFFSET 5', $filter->getParameters()],
        ], SearchablePaymentsForTest::$queries);
        self::assertSame(12, $model->totalRows());
        self::assertCount(1, $result);
        self::assertSame('Example supplier', iterator_to_array($result)[0]->getPaymentSupplier());
    }
}

class SearchablePaymentsForTest extends Payments
{
    public static array $queries = [];

    public static function findBySql($sql, $params)
    {
        self::$queries[] = [$sql, $params];
        if (str_starts_with($sql, 'SELECT COUNT(')) {
            return [static::createFromArray(['payment_id' => '12', 'payment_total' => '12'])];
        }

        return [static::createFromArray([
            'payment_id' => 1, 'payment_supplier' => 'Example supplier',
            'payment_ref' => '123', 'payment_cost_rating' => 2, 'payment_amount' => '600.00',
        ])];
    }
}
