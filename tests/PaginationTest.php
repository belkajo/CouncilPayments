<?php

use Local\CouncilPayments\Model\Db\Pagination;
use PHPUnit\Framework\TestCase;

final class PaginationTest extends TestCase
{
    public function testBuildsLimitAndOffset(): void
    {
        $pagination = new Pagination(3, 5);
        self::assertSame(3, $pagination->getPage());
        self::assertSame(5, $pagination->getPerPage());
        self::assertSame('LIMIT 5 OFFSET 10', $pagination->getSql());
    }

    public function testRejectsInvalidValues(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Pagination(0, 5);
    }

    public function testRejectsOffsetOverflow(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Pagination(PHP_INT_MAX, 5);
    }
}
