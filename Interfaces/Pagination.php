<?php

namespace Local\CouncilPayments\Interfaces;

interface Pagination
{
    public function getPage(): int;

    public function getPerPage(): int;
}
