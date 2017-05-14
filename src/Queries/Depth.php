<?php

namespace Vine\Queries;

use Vine\Node;
use Vine\NodeCollection;

class Depth
{
    public function __invoke(Node $node, $depth = null): Node
    {
        if(!$depth) return $node;

        return $this->recursiveDepth($node->isolatedCopy(), $node->children() , $depth);
    }

    private function recursiveDepth(Node $node, NodeCollection $children, $depth, $currentDepth = 0)
    {
        if($depth <= $currentDepth) return false;

        // Isolate the child nodes before adding them
        $isolatedChildren = new NodeCollection();
        foreach($children as $child)
        {
            $isolatedChildren->add($child->isolatedCopy());
        }

        $node->addChildren($isolatedChildren);
        $currentDepth++;

        // Add next depth of children
        foreach($children as $child)
        {
            $this->recursiveDepth($node,$child->children(),$depth,$currentDepth);
        }

        return $node;
    }
}