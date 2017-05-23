<?php

namespace Vine\Commands;

use Vine\Node;
use Vine\NodeCollection;

class Slice
{
    /**
     * Slice a node from the tree and return a new tree structure where
     * the children from this node get the parent of the extracted Node
     * as their new direct parent
     *
     * @param NodeCollection $nodeCollection
     * @return NodeCollection
     */
    public function __invoke(NodeCollection $nodeCollection, Node ...$sliceNodes): NodeCollection
    {
        // Check if current node is one of the passed nodes to be sliced out
        foreach($sliceNodes as $node)
        {
            // Add children to parent of this node
            foreach($node->children() as $child)
            {
                ($node->isRoot())
                    ? $child->moveToRoot()
                    : $child->move($node->parent());
            }

            $node->removeSelf();
        }

        return $nodeCollection;
    }
}
