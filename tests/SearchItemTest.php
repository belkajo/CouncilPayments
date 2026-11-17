<?php

use Local\CouncilPayments\Interfaces\Db\SearchItem as SearchItemInterface;
use Local\CouncilPayments\Model\Db\SearchItem;
use PHPUnit\Framework\TestCase;

final class SearchItemTest extends TestCase
{
    public function testBuildsParameterizedConditions(): void
    {
        $item = new SearchItem();
        $item->setFieldName('payment_supplier');
        $item->setValue("O'Reilly");
        $item->setOperation(SearchItemInterface::OPERATION_LIKE);
        $parameters = $item->getParameters();
        $key = array_key_first($parameters);
        self::assertSame('`payment_supplier` LIKE :' . $key, $item->getSql());
        self::assertSame("%O'Reilly%", $parameters[$key]);

        $item->setFieldName('payment_cost_rating');
        $item->setOperation(SearchItemInterface::OPERATION_EQ);
        $item->setValue(3);
        self::assertSame('`payment_cost_rating` = :' . $key, $item->getSql());
        self::assertSame(3, $item->getParameters()[$key]);
    }

    public function testRejectsInvalidField(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new SearchItem())->setFieldName('payment_id; DROP TABLE payments');
    }

    public function testRejectsUnknownOperation(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new SearchItem())->setOperation('in');
    }
}
