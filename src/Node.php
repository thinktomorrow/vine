<?php

namespace Vine;

use Vine\Commands\Copy;
use Vine\Commands\Move;
use Vine\Queries\Ancestors;
use Vine\Queries\Count;
use Vine\Queries\Pluck;

class Node
{
    /** @var self */
    private $parent;

    /** @var NodeCollection */
    private $children;

    /** @var mixed */
    private $entry;

    public function __construct($entry)
    {
        $this->entry = $entry;
        $this->children = new NodeCollection();
    }

    public function equals(self $other)
    {
        return $this === $other;
    }

    /**
     * @param array|NodeCollection|Node $children
     *
     * @return Node
     */
    public function addChildren($children): self
    {
        $children = $this->transformToNodeCollection($children);

        $this->children->merge($children);

        array_map(function (Node $child) {
            $child->parent($this);
        }, $children->all());

        return $this;
    }

    public function children(): NodeCollection
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return !$this->children->isEmpty();
    }

    public function sort($key)
    {
        if ($this->hasChildren()) {
            $this->children = $this->children->sort($key);
        }

        return $this;
    }

    public function entry($key = null, $default = null)
    {
        if (!($key === null)) {

            if(is_array($this->entry)) {
                return isset($this->entry[$key]) ? $this->entry[$key] : $default;
            }

            return isset($this->entry->{$key}) ? $this->entry->{$key} : $default;
        }

        return $this->entry;
    }

    public function replaceEntry($entry)
    {
        $this->entry = $entry;
    }

    /**
     * Get the current parent or set it.
     *
     * @param Node|null $parent
     *
     * @return Node
     */
    public function parent(self $parent = null)
    {
        // Without arguments this method returns the parent node
        if (!$parent) {
            return $this->parent;
        }

        $this->parent = $parent;

        return $this;
    }

    /**
     * Remove this node or detaches a child node.
     * This detaches self from parent or when node is passed, it deletes that child node
     * from any depth in the graph. Also removes the entire children tree from that node!
     *
     * @param Node $node
     *
     * @return $this
     */
    public function remove(self $node = null)
    {
        // Remove self from the parent node
        if (null === $node) {
            if (!$this->isRoot()) {
                $this->parent()->remove($this);
                $this->parent = null;
            }

            return $this;
        }

        // remove node from node tree
        return $this->children()->remove($node);
    }

    /**
     * Move a child node to a different parent.
     *
     * @param Node $parent
     *
     * @return mixed
     */
    public function move(self $parent)
    {
        return (new Move())($this, $parent);
    }

    public function moveToRoot()
    {
        return $this->remove();
    }

    /**
     * At which depth does this node resides inside the entire tree.
     *
     * @return int
     */
    public function depth(): int
    {
        if ($this->isRoot()) {
            return 0;
        }

        return $this->parent()->depth() + 1;
    }

    /**
     * count of all direct child nodes.
     *
     * @return int
     */
    public function count(): int
    {
        if ($this->isLeaf()) {
            return 0;
        }

        return $this->children()->count();
    }

    /**
     * Total of all child nodes.
     *
     * @return int
     */
    public function total(): int
    {
        if ($this->isLeaf()) {
            return 0;
        }

        return (new Count())($this);
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
     * @param string|int $key
     * @param array      $values
     *
     * @return NodeCollection
     */
    public function findMany($key, array $values): NodeCollection
    {
        return $this->children()->findMany($key, $values);
    }

    /**
     * @param string|int $key
     * @param mixed      $value
     *
     * @return Node
     */
    public function find($key, $value): self
    {
        return $this->children()->find($key, $value);
    }

    /**
     * @param int|null $depth
     *
     * @return NodeCollection
     */
    public function ancestors($depth = null): NodeCollection
    {
        return (new Ancestors())($this, $depth);
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
        return (new Pluck())($this, $key, $value, $down);
    }

    /**
     * Get flat array of plucked values from child nodes.
     *
     * @param $key
     * @param null $value
     *
     * @return array
     */
    public function pluckAncestors($key, $value = null): array
    {
        return $this->pluck($key, $value, false);
    }

    /**
     * Get a copy of this node.
     *
     * @param int|null $depth
     *
     * @return Node
     */
    public function copy($depth = null): self
    {
        return $depth === 0
                ? new self($this->entry())
                : (new Copy())($this, $depth);
    }

    /**
     * Copy of this node without its parent / children relationships.
     *
     * @return Node
     */
    public function isolatedCopy(): self
    {
        return $this->copy(0);
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
        $node = $this->isolatedCopy();

        return $this->isLeaf() ? $node : $node->addChildren($this->children()->shake($callback));
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
        $node = $this->isolatedCopy();

        return $this->isLeaf() ? $node : $node->addChildren($this->children()->prune($callback));
    }

    /**
     * @param $children
     *
     * @return NodeCollection
     */
    private function transformToNodeCollection($children): NodeCollection
    {
        if (is_array($children)) {
            $children = new NodeCollection(...$children);
        } elseif ($children instanceof self) {
            $children = new NodeCollection($children);
        } elseif (!$children instanceof NodeCollection) {
            throw new \InvalidArgumentException('Invalid children parameter. Accepted types are array or NodeCollection.');
        }

        return $children;
    }

    /**
     * Fetch entry data via a direct call to Node.
     * E.g. $node->name resolves to $node->entry('name').
     *
     * @param string|int $name
     *
     * @return mixed|null|NodeCollection
     */
    public function __get($name)
    {
        if ($name == 'children') {
            return $this->children();
        }

        return $this->entry($name);
    }
}
