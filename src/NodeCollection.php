<?php

namespace Vine;

use Vine\Commands\Flatten;
use Vine\Commands\Inflate;
use Vine\Commands\Prune;
use Vine\Commands\Remove;
use Vine\Commands\Shake;
use Vine\Commands\Slice;
use Vine\Queries\Count;
use Vine\Queries\Find;
use Vine\Queries\Pluck;
use Vine\Sources\ArraySource;

class NodeCollection implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @var Node[]
     */
    protected $nodes;

    public function __construct(Node ...$nodes)
    {
        $this->nodes = $nodes;
    }

    public static function fromArray(array $entries)
    {
        return static::fromSource(new ArraySource($entries));
    }

    public static function fromSource(Source $source)
    {
        return (new NodeCollectionFactory())->fromSource($source);
    }

    public function all()
    {
        return $this->nodes;
    }

    public function first()
    {
        if ($this->isEmpty()) {
            return;
        }

        return reset($this->nodes);
    }

    public function isEmpty(): bool
    {
        return empty($this->nodes);
    }

    /**
     * Add one / many nodes to this collection.
     *
     * @param Node[] $nodes
     *
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
     *
     * @return $this
     */
    public function merge(self $nodeCollection)
    {
        $this->nodes = array_merge($this->nodes, $nodeCollection->all());

        return $this;
    }

    public function map(callable $callback)
    {
        foreach ($this->nodes as $k => $node) {
            $nodes[$k] = call_user_func($callback, $node);
        }

        return $this;
    }

    public function mapRecursive(callable $callback)
    {
        $this->map($callback);

        foreach ($this->nodes as $k => $node) {
            if ($node->hasChildren()) {
                $node->children()->mapRecursive($callback);
            }
        }

        return $this;
    }

    public function each(callable $callback)
    {
        foreach ($this->nodes as $node) {
            call_user_func($callback, $node);
        }

        return $this;
    }

    public function sort($key)
    {
        $nodes = $this->nodes;

        uasort($nodes, function (Node $a, Node $b) use ($key) {
            if ($a->entry($key) == $b->entry($key)) {
                return 0;
            }

            return ($a->entry($key) < $b->entry($key)) ? -1 : 1;
        });

        // Now delegate the sorting to the children
        $collection = (new self(...$nodes))->map(function ($node) use ($key) {
            return $node->sort($key);
        });

        return $collection;
    }

    /**
     * Get a copy of this node collection.
     *
     * @param null|int $depth
     *
     * @return NodeCollection
     */
    public function copy($depth = null): self
    {
        $collection = new self();

        foreach ($this->all() as $child) {
            $collection->add($child->copy($depth));
        }

        return $collection;
    }

    /**
     * Remove the child node.
     *
     * Children of the removed child node will be removed as well. This does not
     * remove the parent / child relations of the removed node. For this
     * the node->remove() should be called instead.
     *
     * @param Node $child
     *
     * @return $this
     */
    public function remove(Node $child)
    {
        return (new Remove())($this, $child);
    }

    /**
     * Return flattened list of all nodes in this collection.
     *
     * @return NodeCollection
     */
    public function flatten()
    {
        return (new Flatten())($this);
    }

    /**
     * Get flat array of plucked values from child nodes.
     *
     * @param string|int      $key
     * @param string|int|null $value
     * @param bool            $down
     *
     * @return array
     */
    public function pluck($key, $value = null, $down = true): array
    {
        $plucks = [];

        foreach ($this->all() as $child) {
            // Keep key identifier in case this is explicitly given
            $plucks = (!($value === null))
                        ? $plucks + (new Pluck())($child, $key, $value, $down)
                        : array_merge($plucks, (new Pluck())($child, $key, $value, $down));
        }

        return $plucks;
    }

    /**
     * Inflate a flattened collection back to its original structure.
     *
     * @return NodeCollection
     */
    public function inflate()
    {
        return (new Inflate())($this);
    }

    /**
     * Slice one or more nodes out of the collection.
     *
     * @param Node[] ...$nodes
     *
     * @return mixed
     */
    public function slice(Node ...$nodes)
    {
        return (new Slice())($this, ...$nodes);
    }

    /**
     * Reduce collection to the nodes that pass the callback
     * Shaking a collection will keep the ancestor structure.
     *
     * @param callable $callback
     *
     * @return self
     */
    public function shake(callable $callback): self
    {
        return (new Shake())($this, $callback);
    }

    /**
     * Same as shaking except that it will not keep the ancestor structure.
     *
     * @param callable $callback
     *
     * @return self
     */
    public function prune(callable $callback): self
    {
        return (new Prune())($this, $callback);
    }

    /**
     * Find many nodes by attribute value.
     *
     * @param $key
     * @param array $values
     *
     * @return NodeCollection
     */
    public function findMany($key, array $values): self
    {
        return (new Find())($this, $key, $values);
    }

    /**
     * Find specific node by attribute value.
     *
     * @param $key
     * @param $value
     *
     * @return Node|null
     */
    public function find($key, $value)
    {
        return (new Find())($this, $key, [$value])->first();
    }

    /**
     * Total of all nodes and their children.
     *
     * @return int
     */
    public function total(): int
    {
        return array_reduce($this->all(), function ($carry, Node $node) {
            return $carry + (new Count())($node);
        }, $this->count());
    }

    public function offsetExists($offset)
    {
        if (!is_string($offset) && !is_int($offset)) {
            return false;
        }

        return array_key_exists($offset, $this->nodes);
    }

    public function offsetGet($offset)
    {
        return $this->nodes[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (null === $offset) {
            $this->nodes[] = $value;
        } else {
            $this->nodes[$offset] = $value;
        }
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
