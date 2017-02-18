<?php

namespace SoapBox\Raven\Utilities;

use Traversable;
use ArrayIterator;
use IteratorAggregate;

class Collection implements IteratorAggregate
{
    private $items;

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function get(string $key, $default = null)
    {
        if ($this->has($key)) {
            return $this->items[$key];
        }

        return $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    public function push($value)
    {
        $this->items[] = $value;
    }

    public function put(string $key, $value)
    {
        $this->items[$key] = $value;
    }

    public function merge(Traversable $items)
    {
        foreach ($items as $item) {
            $this->push($item);
        }
    }
}
