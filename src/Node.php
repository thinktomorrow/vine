<?php

namespace Vine;

use Vine\Queries\Ancestors;
use Vine\Queries\Count;
use Vine\Queries\Depth;
use Vine\Queries\Find;
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

    /**
     * @param array|NodeCollection $children
     * @return Node
     */
    public function addChildren($children): self
    {
        $children = $this->assertChildrenParameter($children);

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
     * Remove parent from this node
     *
     * @return Node
     */
    public function detachParent(): self
    {
        if(!$this->isRoot())
        {
            $this->parent()->detachChild($this);
            $this->parent = null;
        }

        return $this;
    }

    /**
     * Remove child from this node
     *
     * @param Node $child
     * @return Node
     */
    public function detachChild(Node $child): self
    {
        $this->children()->remove($child);

        return $this;
    }

    /**
     * Remove this node. This deletes the node from the graph
     * Also removes all children!
     */
    public function remove()
    {
        foreach($this->children() as $child)
        {
            $child->remove();
        }

        if($this->isRoot())
        {
            // Cannot remove the node when it is a root
            return false;
        }

        $this->parent()->children()->remove($this);
    }

    public function depth(): int
    {
        if($this->isRoot()) return 0;

        return $this->parent()->depth() + 1;
    }

    public function count(): int
    {
        if($this->isLeaf()) return 0;

        return  (new Count)($this);
    }

    public function isLeaf(): bool
    {
        return $this->children->isEmpty();
    }

    public function isRoot(): bool
    {
        return !$this->parent;
    }

    public function findMany($key, array $values): NodeCollection
    {
        return (new Find)($this,$key,$values);
    }

    public function find($key, $value): Node
    {
        return (new Find)($this,$key,[$value])->first();
    }

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
     * Get a Node clone without adjacent relationships
     *
     * @param int $depth
     * @return Node
     */
    public function isolatedCopy($depth = 0): self
    {
        return !$depth
                ? new self($this->entry())
                : (new Depth())($this,$depth);
    }

    /**
     * @param $children
     * @return NodeCollection
     */
    private function assertChildrenParameter($children): NodeCollection
    {
        if (is_array($children)) {
            $children = new NodeCollection(...$children);
        } elseif (is_string($children)) {
            $children = new NodeCollection($children);
        } elseif (!$children instanceof NodeCollection) {
            throw new \InvalidArgumentException('Invalid children parameter. Accepted types are array or NodeCollection.');
        }

        return $children;
    }

    public function __get($name)
    {
        if($name == 'children') return $this->children();

        return $this->entry($name);
    }
}
