<?php

namespace Vine;

use Vine\Queries\Ancestors;
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
    public function children()
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

    public function depth()
    {
        if($this->isRoot()) return 0;

        return $this->parent()->depth() + 1;
    }

    public function isLeaf()
    {
        return $this->children->isEmpty();
    }

    public function isRoot()
    {
        return !$this->parent;
    }

    /**
     * Get subset of the node structure up until a certain depth
     *
     * @param $depth
     */
    public function get($depth)
    {
        //
    }

    public function ancestors($depth = null)
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
     * @return self
     */
    public function isolatedCopy()
    {
        return new self($this->entry());
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