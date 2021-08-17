<?php

namespace Vine\Commands;

use Vine\Node;
use Vine\NodeCollection;

class Slice
{
    /**
     * Slice a node from the tree and return a new tree structure where
     * the children from this node get the parent of the extracted Node
     * as their parent.
     *
     * @param NodeCollection $nodeCollection
     * @param Node[]         $sliceNodes
     *
     * @return NodeCollection
     */
    public function __invoke(NodeCollection $nodeCollection, Node ...$sliceNodes): NodeCollection
    {
        // Check if current node is one of the passed nodes to be sliced out
        foreach ($sliceNodes as $node) {

            // Add children to parent of this node
            foreach ($node->getChildren() as $child) {
                if (($node->isRoot())) {
                    $child->moveToRoot();
                    $nodeCollection->add($child);
                } else {
                    $child->move($node->parent());
                }
            }

            $node->remove();
        }

        $this->removeCollectionChildren($nodeCollection, $sliceNodes);

        // reset keys
        $collection = new NodeCollection();
        $collection->add(...$nodeCollection->all());

        return $collection;
    }

    /**
     * @param NodeCollection $nodeCollection
     * @param Node[]         $sliceNodes
     */
    private function removeCollectionChildren(NodeCollection $nodeCollection, array $sliceNodes)
    {
        // Remove nodes that reside on the root after all the slicing occurred
        foreach ($nodeCollection->all() as $k => $rootChild) {
            foreach ($sliceNodes as $node) {
                if ($rootChild->equals($node)) {
                    unset($nodeCollection[$k]);
                }
            }
        }
    }
}
