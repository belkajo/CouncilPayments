<?php

use Local\CouncilPayments\Interfaces\Db\SearchItem as SearchItemInterface;
use Local\CouncilPayments\Model\Db\Filter;
use Local\CouncilPayments\Model\Db\SearchItem;
use PHPUnit\Framework\TestCase;

final class FilterTest extends TestCase
{
    public function testCombinesConditionsAndParameters(): void
    {
        $supplier = new SearchItem();
        $supplier->setFieldName('payment_supplier');
        $supplier->setValue('Example');
        $supplier->setOperation(SearchItemInterface::OPERATION_LIKE);
        $rating = new SearchItem();
        $rating->setFieldName('payment_cost_rating');
        $rating->setValue(3);
        $rating->setOperation(SearchItemInterface::OPERATION_EQ);
        $filter = new Filter();
        $filter->add($supplier);
        $filter->add($rating);

        self::assertSame('(' . $supplier->getSql() . ') AND (' . $rating->getSql() . ')', $filter->getSql());
        self::assertSame($supplier->getParameters() + $rating->getParameters(), $filter->getParameters());
    }

    public function testEmptyFilterHasNoConditions(): void
    {
        $filter = new Filter();
        self::assertSame('', $filter->getSql());
        self::assertSame([], $filter->getParameters());
    }

    public function testRejectsDuplicateParameters(): void
    {
        $item = new SearchItem();
        $item->setFieldName('payment_supplier');
        $item->setValue('Example');
        $item->setOperation(SearchItemInterface::OPERATION_EQ);
        $filter = new Filter();
        $filter->add($item);
        $filter->add($item);
        $this->expectException(LogicException::class);
        $filter->getParameters();
    }
}
