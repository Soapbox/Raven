<?php namespace SoapBox\Raven\Utils;

use ArrayIterator;
use Countable;
use IteratorAggregate;

class Collection implements Countable, IteratorAggregate
{
    private $items = [];

    /**
     * Get an array containing all of the Sections of this collection
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Get the Section for a given key if it exists. Otherwise,
     * returns null.
     *
     * @param string $key The key to lookup in the collection
     * @return mixed
     */
    public function get($key)
    {
        if ($this->has($key)) {
            return $this->items[$key];
        }

        return null;
    }

    /**
     * Check to see if the collection contains an element for a given key.
     *
     * @param string $key The key to lookup in the collection
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Push an item into this Collection
     *
     * @param mixed $item The item to add to this Collection
     */
    public function push($item)
    {
        $this->items[] = $item;
    }

    /**
     * Add an item into this Collection with a given key
     *
     * @param string $key  The key for the item
     * @param mixed  $item The items to add
     */
    public function add($key, $item)
    {
        $this->items[$key] = $item;
    }

    /**
     * Get the number of items in this Collection
     *
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Determine if the collection is empty
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->count() == 0;
    }

    /**
     * Get the iterator for this Collection
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }
}
