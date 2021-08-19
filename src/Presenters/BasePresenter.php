<?php

namespace Thinktomorrow\Vine\Presenters;

use Thinktomorrow\Vine\Node;
use Thinktomorrow\Vine\NodeCollection;

abstract class BasePresenter
{
    protected $collection;

    public function __construct()
    {
        $this->collection = new NodeCollection();
    }

    public function collection(NodeCollection $collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Render collection.
     *
     * @return string
     */
    abstract public function render();

    /**
     * Render each branch and its children recursively.
     *
     * @param NodeCollection $nodeCollection
     * @param int            $level
     *
     * @return string
     */
    protected function renderRecursiveToString(NodeCollection $nodeCollection, $level = 0): string
    {
        $output = '';

        foreach ($nodeCollection as $node) {
            $output .= $this->template($node, $level);

            if (! $node->isLeafNode()) {
                $output .= $this->renderRecursiveToString($node->getChildNodes(), $level + 1);
            }
        }

        return $output;
    }

    /**
     * Template for a single node entry.
     *
     * @param Node $node
     * @param int  $level
     *
     * @return string
     */
    abstract protected function template(Node $node, $level = 0);
}
