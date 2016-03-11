<?php namespace SoapBox\Raven\DataStructures;

class HashTable
{
    private $table = [];

    private function hash($element)
    {
        return hash('sha256', $element);
    }

    public function add($element)
    {
        $hash = $this->hash($element);
        if (array_key_exists($hash, $this->table)) {
            if (!in_array($element, $this->table[$hash])) {
                $this->table[$hash][] = $element;
            }
        } else {
            $this->table[$hash] = [$element];
        }
    }

    public function contains($element)
    {
        $hash = $this->hash($element);

        return array_key_exists($hash, $this->table) &&
            in_array($element, $this->table[$hash]);
    }
}
