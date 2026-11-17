<?php

namespace Local\CouncilPayments\Model;

use Local\CouncilPayments\Interfaces\Payment as PaymentInterface;

class Payment implements PaymentInterface
{
    public function __construct(
        private int $id = 0,
        private string $supplier = '',
        private string $reference = '',
        private int $rating = 0,
        private string $amount = '0.00',
    ) {
    }

    public function getPaymentId(): int { return $this->id; }
    public function setPaymentId(int $id): void { $this->id = $id; }
    public function getPaymentSupplier(): string { return $this->supplier; }
    public function setPaymentSupplier(string $supplier): void { $this->supplier = $supplier; }
    public function getPaymentRef(): string { return $this->reference; }
    public function setPaymentRef(string $reference): void { $this->reference = $reference; }
    public function getPaymentCostRating(): int { return $this->rating; }
    public function setPaymentCostRating(int $rating): void { $this->rating = $rating; }
    public function getPaymentAmount(): string { return $this->amount; }
    public function setPaymentAmount(string $amount): void { $this->amount = $amount; }
}
