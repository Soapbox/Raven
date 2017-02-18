<?php

namespace SoapBox\Raven\Utilities;

use Traversable;
use ArrayIterator;
use IteratorAggregate;

class Collection implements IteratorAggregate
{
    /**
     * The underlying array of items
     *
     * @var array
     */
    private $items;

    /**
     * Create a new collection
     *
     * @param array $items
     *        An array of items to create the collection with
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Get the iterator for this collection
     *
     * @return \Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Get a value by key, or the default when it does not exsit
     *
     * @param string $key
     *        The key to fetch the value for
     * @param mixed $default
     *        The default return value for when the key does not exist
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if ($this->has($key)) {
            return $this->items[$key];
        }

        return $default;
    }

    /**
     * Check to see if this collection has an item for the given key
     *
     * @param string $key
     *        The key to check
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Push an item onto this collection
     *
     * @param mixed $value
     *        The item to push onto this collection
     *
     * @return \SoapBox\Raven\Utilities\Collection
     *         This collection instance
     */
    public function push($value): Collection
    {
        $this->items[] = $value;
        return $this;
    }

    /**
     * Put an item into this collection with the given key
     *
     * @param string $key
     *        The key to add to the collection
     * @param mixed $value
     *        The value for the key
     *
     * @return \SoapBox\Raven\Utilities\Collection
     *         This collection instance
     */
    public function put(string $key, $value): Collection
    {
        $this->items[$key] = $value;
        return $this;
    }

    /**
     * Merge the given items into this collection
     *
     * @param \Traversable $items
     *        A traversable set of items to merge into this collection
     *
     * @return \SoapBox\Raven\Utilities\Collection
     *         This collection instance
     */
    public function merge(Traversable $items): Collection
    {
        $itemsToAdd = [];

        foreach ($items as $key => $value) {
            $itemsToAdd[$key] = $value;
        }

        $this->items = array_merge($this->items, $itemsToAdd);

        return $this;
    }
}
