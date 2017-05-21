<?php

namespace Tests\Implementations\Presenters;

use Vine\Node;
use Vine\NodeCollection;
use Vine\Presenters\BasePresenter;
use Vine\Presenters\Presenter;

class ArrayPresenter extends BasePresenter implements Presenter
{
    /**
     * Render html filter tree
     *
     * @return array
     */
    public function render()
    {
        return $this->renderRecursiveToArray($this->collection);
    }

    /**
     * Render each node and its children recursively
     *
     * Important! Children are added as subarray right after the parent, not inside him.
     * This allows for a cleaner recursion and faster rendering of the tree.
     *
     * @param NodeCollection $nodeCollection
     * @param int $level
     * @return array
     */
    protected function renderRecursiveToArray(NodeCollection $nodeCollection, $level = 0): array
    {
        $output = [];

        foreach($nodeCollection as $node)
        {
            $output[] = $this->template($node,$level);

            if(!$node->isLeaf()) $output[] = $this->renderRecursiveToArray($node->children(),$level+1);
        }

        return $output;
    }

    protected function template(Node $node,$level = 0)
    {
        return $node->entry(2);
    }
}
