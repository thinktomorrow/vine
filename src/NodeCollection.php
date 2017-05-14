<?php

namespace Vine;

class NodeCollection implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @var Node[]
     */
    private $nodes;

    public function __construct(Node ...$nodes)
    {
        $this->nodes = $nodes;
    }

    public function add(Node ...$nodes)
    {
        $this->nodes = array_merge($this->nodes, $nodes);

        return $this;
    }

    public function merge(self $nodeCollection)
    {
        $this->nodes = array_merge($this->nodes, $nodeCollection->all());

        return $this;
    }

    public function all()
    {
        return $this->nodes;
    }

    public function first()
    {
        return reset($this->nodes);
    }

    public function isEmpty()
    {
        return empty($this->nodes);
    }

    public function offsetExists($offset)
    {
        if(!is_string($offset) && !is_int($offset)) return false;

        return array_key_exists($offset, $this->nodes);
    }

    public function offsetGet($offset)
    {
        return $this->nodes[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if(is_null($offset)) $this->nodes[] = $value;

        else $this->nodes[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->nodes[$offset]);
    }

    public function count()
    {
        return count($this->nodes);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->nodes);
    }
}