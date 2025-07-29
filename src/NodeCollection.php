<?php

namespace Thinktomorrow\Vine;

use Closure;
use Thinktomorrow\Vine\Commands\Flatten;
use Thinktomorrow\Vine\Commands\Inflate;
use Thinktomorrow\Vine\Commands\Prune;
use Thinktomorrow\Vine\Commands\Remove;
use Thinktomorrow\Vine\Commands\Shake;
use Thinktomorrow\Vine\Commands\Slice;
use Thinktomorrow\Vine\Debug\Arrayable;
use Thinktomorrow\Vine\Debug\Debugger;
use Thinktomorrow\Vine\Queries\Count;
use Thinktomorrow\Vine\Queries\Find;
use Thinktomorrow\Vine\Queries\FindFirst;
use Thinktomorrow\Vine\Queries\Pluck;

class NodeCollection implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @var Node[]
     */
    protected array $nodes;

    final public function __construct(array $nodes = [])
    {
        array_map(fn (Node $node) => $node, $nodes);

        $this->nodes = $nodes;
    }

    public static function fromIterable(iterable $items, ?callable $createNode = null): static
    {
        return (new NodeCollectionFactory())->fromIterable(
            new static(),
            $items,
            $createNode ?? fn ($item) => $item instanceof Node ? $item : new DefaultNode($item, new static())
        );
    }

    public static function fromArray(array $entries, ?callable $createNode = null): static
    {
        return static::fromIterable($entries, $createNode);
    }

    public function toArray(): array
    {
        return array_map(fn (Arrayable $node) => $node->toArray(), $this->nodes);
    }

    public function debug(): string|Debugger
    {
        return (new Debugger())->collection($this);
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
     */
    public function merge(NodeCollection $nodeCollection)
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

        foreach ($this->nodes as $node) {
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

    public function eachRecursive(callable $callback)
    {
        $this->each($callback);

        foreach ($this->nodes as $node) {
            if ($node->hasChildNodes()) {
                $node->getChildNodes()->eachRecursive($callback);
            }
        }

        return $this;
    }

    public function sort($key): static
    {
        $nodes = $this->nodes;

        uasort($nodes, function (Node $a, Node $b) use ($key) {
            if ($a->getSortValue($key) == $b->getSortValue($key)) {
                return 0;
            }

            return ($a->getSortValue($key) < $b->getSortValue($key)) ? -1 : 1;
        });

        // Now delegate the sorting to the children
        $collection = (new static(array_values($nodes)))->map(function ($node) use ($key) {
            return $node->sortChildNodes($key);
        });

        return $collection;
    }

    /**
     * Get a copy of this node collection.
     */
    public function copy(?int $depth = null): static
    {
        $collection = new static();

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
    public function removeNode(Node $child): static
    {
        return (new Remove())($this, $child);
    }

    public function remove(\Closure $callback): static
    {
        $nodeCollection = $this->copy();
        $nodesToBeRemoved = (new Find())($nodeCollection, $callback);

        foreach ($nodesToBeRemoved as $nodeToBeRemoved) {
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
     */
    public function pluck(string|int|Closure $key, null|string|int|Closure $value = null, bool $down = true): array
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
     */
    public function inflate(): static
    {
        return (new Inflate())($this);
    }

    /**
     * Slice one or more nodes out of the collection.
     *
     * @param Node ...$nodes
     */
    public function slice(Node ...$nodes): static
    {
        return (new Slice())($this, ...$nodes);
    }

    /**
     * Reduce collection to the nodes that pass the callback
     * Shaking a collection will keep the ancestor structure.
     */
    public function shake(callable $callback): static
    {
        return (new Shake())($this, $callback);
    }

    /**
     * Same as shaking except that it will not keep the ancestor structure.
     */
    public function prune(callable $callback): static
    {
        return (new Prune())($this, $callback);
    }

    /**
     * Find many nodes by attribute value.
     */
    public function findMany(string|Closure $key, ?array $values = null): static
    {
        return (new Find())($this, $key, $values);
    }

    /**
     * Find specific node by attribute value.
     */
    public function find(string|Closure $key, mixed $value = null): ?Node
    {
        return (new FindFirst())($this, $key, $value ? [$value] : null);
    }

    public function findById($value): ?Node
    {
        foreach ($this->nodes as $node) {

            if ($node->getNodeId() == $value) {
                return $node;
            }

            if ($node->hasChildNodes()) {
                if ($result = $node->getChildNodes()->findById($value)) {
                    return $result;
                }
            }
        }

        return null;
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

    public function offsetExists($offset): bool
    {
        if (! is_string($offset) && ! is_int($offset)) {
            return false;
        }

        return array_key_exists($offset, $this->nodes);
    }

    public function offsetGet($offset): mixed
    {
        return $this->nodes[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        if (null === $offset) {
            $this->nodes[] = $value;
        } else {
            $this->nodes[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->nodes[$offset]);
    }

    public function count(): int
    {
        return count($this->nodes);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->nodes);
    }
}
