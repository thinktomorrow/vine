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
        $slicedCollection = new NodeCollection();

        foreach($nodeCollection as $k => $node)
        {
            if(!$node->children()->isEmpty())
            {
                $slicedCollection->merge(
                    $this->__invoke($node->children(), $sliceNodes)
                );
            }

            // Check if current node is one of the passed nodes to be sliced out
            foreach($sliceNodes as $sliceNode)
            {
                if(assertSame($sliceNode, $node))
                {
                    // Add children to parent of this node
                    if($node->isRoot())
                    {
                        foreach($node->children() as $child)
                        {
                            $child->root();
                        }
                    }
                    else{
                        $children = clone $node->children();

                        $node->removeSelf();

                        $node->parent()->addChildren($children);
                    }


                }
            }
        }

        return $roots;
    }
}
