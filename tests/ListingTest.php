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
        $model->expects(self::once())->method('search')
            ->with($collection, self::anything(), self::callback(fn ($pagination) => $pagination->getPage() === 1 && $pagination->getPerPage() === 5))->willReturn($collection);

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
            $model->expects(self::once())->method('search')
                ->with($collection, self::anything(), self::callback(fn ($pagination) => $pagination->getPage() === $expected && $pagination->getPerPage() === 5))->willReturn($collection);

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
        $model->method('search')->willReturn($collection);
        $model->expects(self::once())->method('totalRows')->willReturn(30);

        ob_start();
        try {
            (new Listing($model, $collection))->render(['page' => '2', 'supplier' => 'A & B']);
            $html = ob_get_contents();
        } finally {
            ob_end_clean();
        }

        self::assertStringContainsString('<span aria-current="page">2</span>', $html);
        self::assertStringContainsString('value="A &amp; B"', $html);
        self::assertStringContainsString('type="hidden" name="page" value="2"', $html);
        self::assertStringContainsString('form="payment-search" name="page" value="3"', $html);
    }
}
