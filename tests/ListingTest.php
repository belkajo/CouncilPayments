<?php

use Local\CouncilPayments\Controller\Listing;
use PHPUnit\Framework\TestCase;

final class ListingTest extends TestCase
{
    public function testRendersLayoutAndPayments(): void
    {
        ob_start();
        try {
            (new Listing())->render();
            $html = ob_get_contents();
        } finally {
            ob_end_clean();
        }

        self::assertStringContainsString('Where your money goes', $html);
        self::assertStringContainsString('Supplier name', $html);
        self::assertStringContainsString('499778', $html);
        self::assertSame(5, substr_count($html, '<td class="rating"'));
        self::assertSame(3, substr_count($html, 'aria-label="3 out of 5 pounds"'));
        self::assertSame(2, substr_count($html, 'aria-label="2 out of 5 pounds"'));
        self::assertSame(13, substr_count($html, '/RatingOn.png'));
        self::assertSame(12, substr_count($html, '/RatingOff.png'));
    }
}
