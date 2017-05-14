<?php

namespace Vine;

use Vine\Queries\Ancestors;
use Vine\Queries\Depth;
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
     * @return $this
     */
    public function addChildren($children)
    {
        $children = $this->assertChildrenParameter($children);

        $this->children->merge($children);

        array_map(function($child){
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

    public function removeParent()
    {
        $this->parent = null;

        return $this;
    }

    public function depth(): int
    {
        if($this->isRoot()) return 0;

        return $this->parent()->depth() + 1;
    }

    public function isLeaf(): bool
    {
        return $this->children->isEmpty();
    }

    public function isRoot(): bool
    {
        return !$this->parent;
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
     * @return array
     */
    public function pluck($key, $value = null): array
    {
        return (new Pluck)($this, $key, $value);
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
}