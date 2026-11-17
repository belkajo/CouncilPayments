<?php

use Local\CouncilPayments\Model\Collection\PaymentCollection;
use Local\CouncilPayments\Controller\Listing;
use Local\CouncilPayments\Model\Payment;
use Local\CouncilPayments\Model\Payments;
use PHPUnit\Framework\TestCase;

final class ListingTest extends TestCase
{
    public function testRendersInjectedCollection(): void
    {
        $collection = new PaymentCollection();
        $collection->add(new Payment(1, 'Supplier name', '499778', 3, '1620.00'));
        $model = $this->createMock(Payments::class);
        $model->expects(self::once())->method('loadPage')
            ->with($collection, 1, 5)->willReturn($collection);

        ob_start();
        try {
            (new Listing($model, $collection))->render();
            $html = ob_get_contents();
        } finally {
            ob_end_clean();
        }

        self::assertStringContainsString('Where your money goes', $html);
        self::assertStringContainsString('Supplier name', $html);
        self::assertStringContainsString('499778', $html);
        self::assertStringContainsString('£1,620.00', $html);
        self::assertStringContainsString('aria-label="3 out of 5 pounds"', $html);
        self::assertSame(3, substr_count($html, '/RatingOn.png'));
        self::assertSame(2, substr_count($html, '/RatingOff.png'));
    }

    public function testPageParameterIsPassedToModel(): void
    {
        foreach ([['2', 2], ['0', 1], ['invalid', 1], [[], 1]] as [$input, $expected]) {
            $collection = new PaymentCollection();
            $model = $this->createMock(Payments::class);
            $model->expects(self::once())->method('loadPage')
                ->with($collection, $expected, 5)->willReturn($collection);

            ob_start();
            try {
                (new Listing($model, $collection))->render(['page' => $input]);
            } finally {
                ob_end_clean();
            }
        }
    }

    public function testPaginationDisplaysCurrentPageAndLinks(): void
    {
        $collection = new PaymentCollection();
        $model = $this->createMock(Payments::class);
        $model->method('loadPage')->willReturn($collection);
        $model->expects(self::once())->method('totalRows')->willReturn(30);

        ob_start();
        try {
            (new Listing($model, $collection))->render(['page' => '2', 'supplier' => 'A & B']);
            $html = ob_get_contents();
        } finally {
            ob_end_clean();
        }

        self::assertStringContainsString('<span aria-current="page">2</span>', $html);
        self::assertStringContainsString('supplier=A+%26+B', $html);
        self::assertStringContainsString('page=1', $html);
        self::assertStringContainsString('page=3', $html);
    }
}
