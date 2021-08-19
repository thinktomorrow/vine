<?php

namespace Thinktomorrow\Vine;

use Closure;
use Thinktomorrow\Vine\Commands\Flatten;
use Thinktomorrow\Vine\Commands\Inflate;
use Thinktomorrow\Vine\Commands\Prune;
use Thinktomorrow\Vine\Commands\Remove;
use Thinktomorrow\Vine\Commands\Shake;
use Thinktomorrow\Vine\Commands\Slice;
use Thinktomorrow\Vine\Queries\Count;
use Thinktomorrow\Vine\Queries\Find;
use Thinktomorrow\Vine\Queries\FindFirst;
use Thinktomorrow\Vine\Queries\Pluck;
use Thinktomorrow\Vine\Sources\ArraySource;

class NodeCollection implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @var Node[]
     */
    protected array $nodes;

    public function __construct(array $nodes = [])
    {
        array_map(fn(Node $node) => $node, $nodes);

        $this->nodes = $nodes;
    }

    public static function fromArray(array $entries): self
    {
        return static::fromSource(new ArraySource($entries));
    }

    public static function fromSource(Source $source): self
    {
        return (new NodeCollectionFactory())->fromSource($source);
    }

    public function all(): array
    {
        return $this->nodes;
    }

    public function first(): ?Node
    {
        if ($this->isEmpty()) {
            return null;
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
     * @param Node ...$nodes
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
            if ($node->hasChildNodes()) {
                $node->getChildNodes()->mapRecursive($callback);
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
            if ($a->getNodeEntry($key) == $b->getNodeEntry($key)) {
                return 0;
            }

            return ($a->getNodeEntry($key) < $b->getNodeEntry($key)) ? -1 : 1;
        });

        // Now delegate the sorting to the children
        $collection = (new self(array_values($nodes)))->map(function ($node) use ($key) {
            return $node->sortChildNodes($key);
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
            $collection->add($child->copyNode($depth));
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
    public function removeNode(Node $child): self
    {
        return (new Remove())($this, $child);
    }

    public function remove(\Closure $callback): self
    {
        $nodesToBeRemoved = (new Find())($this, $callback);

        $nodeCollection = new self($this->all());

        foreach($nodesToBeRemoved as $nodeToBeRemoved){
            $nodeCollection = (new Remove())($nodeCollection, $nodeToBeRemoved);
        }

        return $nodeCollection;
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
            $plucks = (! ($value === null))
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
     * @param string|Closure $key
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
     * @param string|Closure $key
     * @param mixed|null $value
     *
     * @return Node|null
     */
    public function find($key, $value = null): ?Node
    {
        return (new FindFirst())($this, $key, $value ? [$value] : null);
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
        if (! is_string($offset) && ! is_int($offset)) {
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
