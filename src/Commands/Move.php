<?php

namespace Vine\Commands;

use Vine\Node;

class Move
{
    /**
     * Move a child node to a different parent
     *
     * @param Node $node
     * @param Node $parent
     * @return Node
     */
    public function __invoke(Node $node, Node $parent): Node
    {
        if(!$node->isRoot()) $node->parent()->remove($node);

        $parent->addChildren($node);

        return $node;
    }
}
