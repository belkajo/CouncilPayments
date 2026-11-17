<?php

namespace Local\CouncilPayments\Controller;

class Listing
{
    public function render(): void
    {
        // Placeholder data until the database-backed listing is implemented.
        $payments = [
            ['payment_supplier' => 'Supplier name', 'payment_ref' => '499778', 'payment_amount' => '£1,620', 'payment_cost_rating' => 3],
            ['payment_supplier' => 'Supplier name', 'payment_ref' => '499778', 'payment_amount' => '£620', 'payment_cost_rating' => 2],
            ['payment_supplier' => 'Supplier name', 'payment_ref' => '499778', 'payment_amount' => '£1,620', 'payment_cost_rating' => 3],
            ['payment_supplier' => 'Supplier name', 'payment_ref' => '499778', 'payment_amount' => '£620', 'payment_cost_rating' => 2],
            ['payment_supplier' => 'Supplier name', 'payment_ref' => '499778', 'payment_amount' => '£1,620', 'payment_cost_rating' => 3],
        ];

        require __DIR__ . '/../view/templates/layout.phtml';
    }
}
