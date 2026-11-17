<?php

namespace Local\CouncilPayments\Interfaces\Db;


interface Filter extends SqlExpression
{
    public function add(SearchItem $searchItem): void;

    /** @return array<string, string|int|float> */
    public function getParameters(): array;
}
