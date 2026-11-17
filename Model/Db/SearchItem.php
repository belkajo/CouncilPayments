<?php

namespace Local\CouncilPayments\Model\Db;

use InvalidArgumentException;
use Local\CouncilPayments\Interfaces\Db\SearchItem as SearchItemInterface;

class SearchItem implements SearchItemInterface
{
    private string $fieldName;
    private mixed $value;
    private string $operation;

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function setFieldName(string $fieldName): self
    {
        if (!preg_match('/\A[a-zA-Z_][a-zA-Z0-9_]*\z/D', $fieldName)) {
            throw new InvalidArgumentException('Invalid field name');
        }
        $this->fieldName = $fieldName;
        return $this;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): self
    {
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            throw new InvalidArgumentException('Search value must be a string or number');
        }
        $this->value = $value;
        return $this;
    }

    public function getOperation(): string
    {
        return $this->operation;
    }

    public function setOperation(string $operation): self
    {
        if (!in_array($operation, [self::OPERATION_EQ, self::OPERATION_LIKE], true)) {
            throw new InvalidArgumentException('Unsupported search operation');
        }
        $this->operation = $operation;
        return $this;
    }

    public function getSql(): string
    {
        return '`' . $this->fieldName . '` ' . $this->getSqlOperator() . ' :' . $this->parameterName();
    }

    private function getSqlOperator(): string
    {
        switch ($this->operation) {
            case self::OPERATION_EQ:
                return '=';
            case self::OPERATION_LIKE:
                return 'LIKE';
            default:
                throw new InvalidArgumentException('Unsupported search operation');
        }
    }

    public function getParameters(): array
    {
        $value = $this->operation === self::OPERATION_LIKE ? '%' . $this->value . '%' : $this->value;

        return [$this->parameterName() => $value];
    }

    private function parameterName(): string
    {
        return 'search_' . spl_object_id($this) . '_value';
    }
}
