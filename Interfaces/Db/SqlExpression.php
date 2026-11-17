<?php

namespace Local\CouncilPayments\Interfaces\Db;

interface SqlExpression
{
    public function getSql(): string;
}
