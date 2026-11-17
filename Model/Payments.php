<?php

namespace Local\CouncilPayments\Model;

use D3R\Db;
use D3R\Model;
use Local\CouncilPayments\Interfaces\Db\Filter;
use Local\CouncilPayments\Model\Collection\PaymentCollection;
use Local\CouncilPayments\Model\Db\Pagination;
use Local\CouncilPayments\Interfaces\Payment as PaymentInterface;
use RuntimeException;

class Payments extends Model
{
    protected static $_tableName = 'payments';
    protected static $_itemName = 'payment';
    private int $total = 0;

    public function search(PaymentCollection $collection, ?Filter $filter, ?Pagination $pagination): PaymentCollection
    {
        $where = $filter?->getSql() ?: '1';
        $params = $filter?->getParameters() ?? [];
        $count = static::findBySql('SELECT COUNT(*) AS payment_id, COUNT(*) AS payment_total FROM ' . static::tableName() . ' WHERE ' . $where, $params);
        if ($count === false || !isset($count[0])) {
            throw new RuntimeException('Unable to count payments');
        }
        $this->total = (int) $count[0]->field('total');

        $records = static::findBySql(
            'SELECT * FROM ' . static::tableName() . ' WHERE ' . $where . ' ORDER BY payment_id' . ($pagination ? ' ' . $pagination->getSql() : ''),
            $params
        );
        foreach ($records ?: [] as $record) {
            $collection->add($record->toPayment());
        }

        return $collection;
    }

    public function totalRows(): int
    {
        return $this->total;
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
}
