<?php

namespace Local\CouncilPayments\Model;

use D3R\Db;
use D3R\Model;
use Local\CouncilPayments\Model\Collection\PaymentCollection;
use Local\CouncilPayments\Interfaces\Payment as PaymentInterface;
use RuntimeException;

class Payments extends Model
{
    protected static $_tableName = 'payments';
    protected static $_itemName = 'payment';

    public static function loadPayment(int $id): ?PaymentInterface
    {
        $record = static::find($id);

        return $record === false ? null : $record->toPayment();
    }

    public function loadPage(PaymentCollection $collection, int $page, int $perPage = 5): PaymentCollection
    {
        $records = static::findPage($page, $perPage, '1 ORDER BY payment_id');
        if ($records === false) {
            throw new RuntimeException('Unable to fetch payments');
        }

        foreach ($records as $record) {
            $collection->add($record->toPayment());
        }

        return $collection;
    }

    public function totalRows(): int
    {
        return (int) Db::get()->numRows();
    }

    public function toPayment(): PaymentInterface
    {
        return new Payment(
            (int) $this->field('id'),
            $this->field('supplier'),
            $this->field('ref'),
            (int) $this->field('cost_rating'),
            (string) $this->field('amount')
        );
    }

    public function store(PaymentInterface $payment): void
    {
        $this->_id = false;
        $this->setFromArray([
            'payment_supplier' => $payment->getPaymentSupplier(),
            'payment_ref' => $payment->getPaymentRef(),
            'payment_cost_rating' => $payment->getPaymentCostRating(),
            'payment_amount' => $payment->getPaymentAmount(),
        ]);

        if ($payment->getPaymentId() !== 0) {
            $this->setId($payment->getPaymentId());
        }

        $this->save();
        $payment->setPaymentId((int) $this->_id);
    }

    public function delete(PaymentInterface $payment): void
    {
        if ($payment->getPaymentId() <= 0) {
            throw new RuntimeException('Cannot delete a payment without an ID');
        }

        if (Db::get()->delete(static::tableName(), 'payment_id = :id', ['id' => $payment->getPaymentId()]) === false) {
            throw new RuntimeException('Unable to delete payment');
        }
    }
}
