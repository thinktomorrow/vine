<?php

namespace Vine\Presenters;

use Vine\Node;
use Vine\NodeCollection;
use Vine\Tree;

abstract class BasePresenter
{
    protected $tree;

    public function __construct()
    {
        // Empty tree
        $this->tree = new Tree(new NodeCollection(), new NodeCollection());
    }

    public function tree(Tree $tree)
    {
        $this->tree = $tree;

        return $this;
    }

    /**
     * Render tree
     *
     * @return string
     */
    abstract public function render();

    /**
     * Render each branch and its children recursively
     *
     * @param NodeCollection $nodeCollection
     * @param int $level
     * @return string
     */
    protected function renderRecursiveToString(NodeCollection $nodeCollection, $level = 0): string
    {
        $output = '';

        foreach($nodeCollection as $node)
        {
            $output .= $this->template($node,$level);

            if(!$node->isLeaf()) $output .= $this->renderRecursiveToString($node->children(),$level+1);
        }

        return $output;
    }

    /**
     * Template for a single node entry.
     *
     * @param Node $node
     * @param int $level
     * @return string
     */
    abstract protected function template(Node $node,$level = 0);
}
