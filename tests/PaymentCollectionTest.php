<?php

use Local\CouncilPayments\Model\Collection\PaymentCollection;
use Local\CouncilPayments\Model\Payment;
use PHPUnit\Framework\TestCase;

final class PaymentCollectionTest extends TestCase
{
    public function testCanCountAndIteratePaymentsInOrder(): void
    {
        $collection = new PaymentCollection();
        $first = new Payment(1, 'First', '1', 2, '100.00');
        $second = new Payment(2, 'Second', '2', 3, '200.00');
        $collection->add($first);
        $collection->add($second);

        self::assertCount(2, $collection);
        self::assertSame([$first, $second], iterator_to_array($collection));
    }

}
