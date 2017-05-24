<?php

namespace Vine;

use Vine\Commands\Move;
use Vine\Commands\Shake;
use Vine\Queries\Ancestors;
use Vine\Queries\Count;
use Vine\Commands\Copy;
use Vine\Queries\Pluck;

class Node
{
    /**
     * @var self
     */
    private $parent;

    /**
     * @var NodeCollection
     */
    private $children;

    /**
     * @var mixed
     */
    private $entry;

    public function __construct($entry)
    {
        $this->entry = $entry;
        $this->children = new NodeCollection;
    }

    public function equals(self $other)
    {
        return $this === $other;
    }

    /**
     * @param array|NodeCollection $children
     * @return Node
     */
    public function addChildren($children): self
    {
        $children = $this->transformToNodeCollection($children);

        $this->children->merge($children);

        array_map(function(Node $child){
            $child->parent($this);
        },$children->all());

        return $this;
    }

    /**
     * @return NodeCollection
     */
    public function children(): NodeCollection
    {
        return $this->children;
    }

    public function entry($key = null)
    {
        if(!is_null($key))
        {
            return (is_array($this->entry) && isset($this->entry[$key]))
                    ? $this->entry[$key]
                    : (is_object($this->entry) ? $this->entry->{$key} : null);
        }

        return $this->entry;
    }

    /**
     * Replace entire entry value
     *
     * @param $entry
     */
    public function replaceEntry($entry)
    {
        $this->entry = $entry;
    }

    /**
     * @param Node $parent
     * @return $this
     */
    public function parent(self $parent = null)
    {
        // Without arguments this method returns the parent node
        if(!$parent) return $this->parent;

        $this->parent = $parent;

        return $this;
    }

    /**
     * Remove this node from the tree / parent
     * If this node is root, nothing is done
     *
     * @return Node
     */
    public function removeSelf(): self
    {
        if(!$this->isRoot())
        {
            $this->parent()->remove($this);
            $this->parent = null;
        }

        return $this;
    }

    /**
     * Remove (detaches) a child node. This deletes the node from any depth in the graph
     * Also removes the entire children tree from that node!
     *
     * @param Node $node
     * @return $this
     */
    public function remove(self $node)
    {
        return $this->children()->remove($node);
    }

    /**
     * Move a child node to a different parent
     *
     * @param Node $parent
     * @return mixed
     */
    public function move(self $parent)
    {
        return (new Move())($this,$parent);
    }

    public function moveToRoot()
    {
        return $this->removeSelf();
    }

    /**
     * At which depth does this node resides inside the entire tree
     *
     * @return int
     */
    public function depth(): int
    {
        if($this->isRoot()) return 0;

        return $this->parent()->depth() + 1;
    }

    /**
     * count of all direct child nodes
     *
     * @return int
     */
    public function count(): int
    {
        if($this->isLeaf()) return 0;

        return $this->children()->count();
    }

    /**
     * Total of all child nodes
     *
     * @return int
     */
    public function total(): int
    {
        if($this->isLeaf()) return 0;

        return (new Count)($this);
    }

    /**
     * @return bool
     */
    public function isLeaf(): bool
    {
        return $this->children->isEmpty();
    }

    /**
     * @return bool
     */
    public function isRoot(): bool
    {
        return !$this->parent;
    }

    /**
     * @param $key
     * @param array $values
     * @return NodeCollection
     */
    public function findMany($key, array $values): NodeCollection
    {
        return $this->children()->findMany($key, $values);
    }

    /**
     * @param $key
     * @param $value
     * @return Node
     */
    public function find($key, $value): Node
    {
        return $this->children()->find($key, $value);
    }

    /**
     * @param null $depth
     * @return NodeCollection
     */
    public function ancestors($depth = null): NodeCollection
    {
        return (new Ancestors)($this, $depth);
    }

    /**
     * Get flat array of plucked values from child nodes
     *
     * @param $key
     * @param null $value
     * @param bool $down
     * @return array
     */
    public function pluck($key, $value = null, $down = true): array
    {
        return (new Pluck)($this, $key, $value, $down);
    }

    /**
     * Get flat array of plucked values from child nodes
     *
     * @param $key
     * @param null $value
     * @return array
     */
    public function pluckAncestors($key, $value = null): array
    {
        return $this->pluck($key, $value, false);
    }

    /**
     * Get a copy of this node
     *
     * @param null|int $depth
     * @return Node
     */
    public function copy($depth = null): self
    {
        return $depth === 0
                ? new self($this->entry())
                : (new Copy())($this,$depth);
    }

    /**
     * Copy of this node without its parent / children relationships
     *
     * @return Node
     */
    public function isolatedCopy(): self
    {
        return $this->copy(0);
    }

    /**
     * Reduce collection to the nodes that pass the callback
     * Shaking a collection will keep the ancestor structure
     *
     * @param callable $callback
     * @return NodeCollection
     */
    public function shake(Callable $callback): self
    {
        return (new Shake())($this, $callback);
    }

    /**
     * @param $children
     * @return NodeCollection
     */
    private function transformToNodeCollection($children): NodeCollection
    {
        if (is_array($children)) {
            $children = new NodeCollection(...$children);
        } elseif ($children instanceof Node) {
            $children = new NodeCollection($children);
        } elseif (!$children instanceof NodeCollection) {
            throw new \InvalidArgumentException('Invalid children parameter. Accepted types are array or NodeCollection.');
        }

        return $children;
    }

    /**
     * Fetch entry data via a direct call to Node.
     * E.g. $node->name resolves to $node->entry('name')
     *
     * @param $name
     * @return mixed|null|NodeCollection
     */
    public function __get($name)
    {
        if($name == 'children') return $this->children();

        return $this->entry($name);
    }
}
