<?php

namespace Local\CouncilPayments\Controller;

use Local\CouncilPayments\Model\Collection\PaymentCollection;
use Local\CouncilPayments\Model\Payments;

class Listing
{
    public function __construct(
        private Payments $paymentsModel,
        private PaymentCollection $collection,
    ) {
    }

    public function render(array $query = []): void
    {
        $page = filter_var($query['page'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);
        $page = $page === false ? 1 : $page;
        $payments = $this->paymentsModel->loadPage($this->collection, $page, 5);
        $totalPages = (int) ceil($this->paymentsModel->totalRows() / 5);

        require __DIR__ . '/../view/templates/layout.phtml';
    }
}
