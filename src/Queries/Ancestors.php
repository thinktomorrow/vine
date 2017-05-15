<?php

namespace Vine\Queries;

use Vine\Node;
use Vine\NodeCollection;

class Ancestors
{
    /**
     * @param Node $node
     * @param null $depth
     * @return NodeCollection
     */
    public function __invoke(Node $node, $depth = null): NodeCollection
    {
        $ancestors = new NodeCollection;
        $currentDepth = 0;

        while($parent = $node->parent())
        {
            if(!is_null($depth) && $currentDepth >= $depth) break;
            $currentDepth++;

            $node = $parent;
            $ancestors->add($parent);
        }

        return new NodeCollection(...array_reverse($ancestors->all()));
    }
}
