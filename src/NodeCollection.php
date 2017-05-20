<?php

namespace Vine;

use Vine\Commands\Remove;
use Vine\Queries\Find;

class NodeCollection implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @var Node[] | NodeCollection
     */
    protected $nodes;

    public function __construct(Node ...$nodes)
    {
        $this->nodes = $nodes;
    }

    public function all()
    {
        return $this->nodes;
    }

    public function first()
    {
        if($this->isEmpty()) return null;

        return reset($this->nodes);
    }

    public function isEmpty(): bool
    {
        return empty($this->nodes);
    }

    /**
     * Add one / many nodes to this collection
     *
     * @param Node[] ...$nodes
     * @return $this
     */
    public function add(Node ...$nodes)
    {
        $this->nodes = array_merge($this->nodes, $nodes);

        return $this;
    }

    /**
     * Merge a collection into current one.
     *
     * @param NodeCollection $nodeCollection
     * @return $this
     */
    public function merge(self $nodeCollection)
    {
        $this->nodes = array_merge($this->nodes, $nodeCollection->all());

        return $this;
    }

    /**
     * Remove the child node
     *
     * Please note that all descendants of this
     * child node will be removed as well.
     *
     * @param Node $child
     * @return $this
     */
    public function remove(Node $child)
    {
        return (new Remove())($this,$child);
    }

    /**
     * Find many nodes by attribute value
     *
     * @param $key
     * @param array $values
     * @return NodeCollection
     */
    public function findMany($key, array $values): NodeCollection
    {
        return (new Find)($this,$key,$values);
    }

    /**
     * Find specific node by attribute value
     *
     * @param $key
     * @param $value
     * @return Node|null
     */
    public function find($key, $value)
    {
        return (new Find)($this,$key,[$value])->first();
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
