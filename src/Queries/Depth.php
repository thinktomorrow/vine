<?php

namespace Vine\Queries;

use Vine\Node;
use Vine\NodeCollection;

class Depth
{
    /**
     * Keep in mind that Node::isolatedCopy() utilises the Depth class when used with the depth parameter.
     * So here the Node::isolatedCopy() must never be passed a param to avoid infinite call loop.
     *
     * @param Node $node
     * @param null $depth
     * @return Node
     */
    public function __invoke(Node $node, $depth = null): Node
    {
        if(!$depth) return $node;

        return $this->recursiveDepth($node->isolatedCopy(), $node->children() , $depth);
    }

    /**
     * @param Node $node
     * @param NodeCollection $children
     * @param $depth
     * @param int $currentDepth
     * @return bool|Node
     */
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
