<?php

namespace Vine\Presenters;

use Vine\Node;
use Vine\NodeCollection;

class ArrayPresenter extends BasePresenter implements Presenter
{
    /**
     * Render html filter tree.
     *
     * @return array
     */
    public function render()
    {
        return $this->renderRecursiveToArray($this->collection);
    }

    /**
     * Render each node and its children recursively.
     *
     * Children are added inside a specific 'children' property and are not nested inside the parent element itself.
     * This allows for a cleaner recursion and faster rendering of the tree.
     *
     * @param NodeCollection $nodeCollection
     * @param int            $level
     *
     * @return array
     */
    protected function renderRecursiveToArray(NodeCollection $nodeCollection, $level = 0): array
    {
        $output = [];

        foreach ($nodeCollection as $node) {
            $output[] = $this->template($node, $level);

            if (!$node->isLeaf()) {
                $output[] = $this->renderRecursiveToArray($node->getChildren(), $level + 1);
            }
        }

        return $output;
    }

    protected function template(Node $node, $level = 0)
    {
        return $node->entry(2);
    }
}
