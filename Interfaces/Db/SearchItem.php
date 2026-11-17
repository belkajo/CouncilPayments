<?php

namespace Local\CouncilPayments\Interfaces\Db;


interface SearchItem extends SqlExpression
{
    public const OPERATION_EQ = 'eq';
    public const OPERATION_LIKE = 'like';

    public function getFieldName(): string;

    public function setFieldName(string $fieldName): self;

    public function getValue(): mixed;

    public function setValue(mixed $value): self;

    public function getOperation(): string;

    public function setOperation(string $operation): self;

    /** @return array<string, string|int|float> */
    public function getParameters(): array;
}
