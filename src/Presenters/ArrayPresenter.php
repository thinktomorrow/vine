<?php

namespace Thinktomorrow\Vine\Presenters;

use Thinktomorrow\Vine\Node;
use Thinktomorrow\Vine\NodeCollection;

class ArrayPresenter extends BasePresenter
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
            $output[] = $node->getNodeEntry(2);

            if ($node->hasChildNodes()) {
                $output[] = $this->renderRecursiveToArray($node->getChildNodes(), $level + 1);
            }
        }

        return $output;
    }

    protected function template(Node $node, $level = 0)
    {
        return $node->getNodeEntry(2);
    }
}
