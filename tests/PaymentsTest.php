<?php

use D3R\Model;
use Local\CouncilPayments\Interfaces\Payment as PaymentInterface;
use Local\CouncilPayments\Model\Payments;
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
}
