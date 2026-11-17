<?php

namespace Local\CouncilPayments\Model\Db;

use InvalidArgumentException;
use Local\CouncilPayments\Interfaces\Db\SqlExpression;
use Local\CouncilPayments\Interfaces\Pagination as PaginationInterface;

class Pagination implements PaginationInterface, SqlExpression
{
    public function __construct(private int $page, private int $perPage)
    {
        if ($page < 1 || $perPage < 1 || $page - 1 > intdiv(PHP_INT_MAX, $perPage)) {
            throw new InvalidArgumentException('Invalid pagination values');
        }
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getSql(): string
    {
        $offset = ($this->page - 1) * $this->perPage;

        return 'LIMIT ' . $this->perPage . ' OFFSET ' . $offset;
    }
}
