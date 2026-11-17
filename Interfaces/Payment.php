<?php

namespace Local\CouncilPayments\Interfaces;

interface Payment
{
    public function getPaymentId(): int;

    public function setPaymentId(int $id): void;

    public function getPaymentSupplier(): string;

    public function setPaymentSupplier(string $supplier): void;

    public function getPaymentRef(): string;

    public function setPaymentRef(string $reference): void;

    public function getPaymentCostRating(): int;

    public function setPaymentCostRating(int $rating): void;

    public function getPaymentAmount(): string;

    public function setPaymentAmount(string $amount): void;
}
