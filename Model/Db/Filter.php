<?php

namespace Local\CouncilPayments\Model\Db;

use Local\CouncilPayments\Interfaces\Db\Filter as FilterInterface;
use Local\CouncilPayments\Interfaces\Db\SearchItem;
use LogicException;

class Filter implements FilterInterface
{
    /** @var list<SearchItem> */
    private array $items = [];

    public function add(SearchItem $searchItem): void
    {
        $this->items[] = $searchItem;
    }

    public function getSql(): string
    {
        return implode(' AND ', array_map(
            static fn (SearchItem $item): string => '(' . $item->getSql() . ')',
            $this->items
        ));
    }

    public function getParameters(): array
    {
        $parameters = [];
        foreach ($this->items as $item) {
            foreach ($item->getParameters() as $name => $value) {
                if (array_key_exists($name, $parameters)) {
                    throw new LogicException('Duplicate search parameter: ' . $name);
                }
                $parameters[$name] = $value;
            }
        }

        return $parameters;
    }
}
