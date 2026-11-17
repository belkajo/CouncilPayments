<?php

namespace Local\CouncilPayments\Model\Collection;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Local\CouncilPayments\Interfaces\Payment;
use Traversable;

/** @implements IteratorAggregate<int, Payment> */
class PaymentCollection implements Countable, IteratorAggregate
{
    /** @var list<Payment> */
    private array $payments = [];

    public function add(Payment $payment): void
    {
        $this->payments[] = $payment;
    }

    public function count(): int
    {
        return count($this->payments);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->payments);
    }
}
