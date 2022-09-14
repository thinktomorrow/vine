<?php

namespace Thinktomorrow\Vine\Commands;

use Thinktomorrow\Vine\Node;
use Thinktomorrow\Vine\NodeCollection;

class Slice
{
    /**
     * Slice a node from the tree and return a new tree structure where
     * the children from this node get the parent of the extracted Node
     * as their parent.
     *
     * @param NodeCollection $nodeCollection
     * @param Node ...$sliceNodes
     * @return NodeCollection
     */
    public function __invoke(NodeCollection $nodeCollection, Node ...$sliceNodes): NodeCollection
    {
        // Check if current node is one of the passed nodes to be sliced out
        foreach ($sliceNodes as $node) {
            // Add children to parent of this node
            /** @var Node $child */
            foreach ($node->getChildNodes() as $child) {
                if (($node->isRootNode())) {
                    $child->moveNodeToRoot();
                    $nodeCollection->add($child);
                } else {
                    $child->moveToParentNode($node->getParentNode());
                }
            }

            if ($node->hasParentNode()) {
                $node->getParentNode()->removeNode($node);
            }
        }

        $this->removeCollectionChildren($nodeCollection, $sliceNodes);

        // reset keys
        $className = get_class($nodeCollection);
        $collection = new $className();
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
                if ($rootChild->equalsNode($node)) {
                    unset($nodeCollection[$k]);
                }
            }
        }
    }
}
