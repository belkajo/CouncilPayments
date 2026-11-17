<?php

namespace Local\CouncilPayments\Controller;

use Local\CouncilPayments\Model\Collection\PaymentCollection;
use Local\CouncilPayments\Model\Db\Filter;
use Local\CouncilPayments\Model\Db\Pagination;
use Local\CouncilPayments\Model\Db\SearchItem;
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
        $page = $page === false ? 1 : min($page, intdiv(PHP_INT_MAX, 5));
        $pagination = new Pagination($page, 5);
        $filter = new Filter();

        $supplier = is_string($query['supplier'] ?? null) ? trim($query['supplier']) : '';

        if ($supplier !== '') {
            $item = (new SearchItem())
                ->setFieldName('payment_supplier')
                ->setValue($supplier)
                ->setOperation(SearchItem::OPERATION_LIKE);
            $filter->add($item);
        }
        $rating = filter_var($query['rating'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 5],
        ]);
        if ($rating !== false) {
            $item = (new SearchItem())
                ->setFieldName('payment_cost_rating')
                ->setValue($rating)
                ->setOperation(SearchItem::OPERATION_EQ);
            $filter->add($item);
        }
        $payments = $this->paymentsModel->search($this->collection, $filter, $pagination);
        $totalPages = (int) ceil($this->paymentsModel->totalRows() / $pagination->getPerPage());

        if ($page > max(1, $totalPages)) {
            $page = max(1, $totalPages);
            $pagination = new Pagination($page, $pagination->getPerPage());
            $payments = $this->paymentsModel->search(new PaymentCollection(), $filter, $pagination);
        }

        require __DIR__ . '/../view/templates/layout.phtml';
    }
}
