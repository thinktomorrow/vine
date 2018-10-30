<?php

namespace Vine\Commands;

use Vine\Node;
use Vine\NodeCollection;

class Remove
{
    /**
     * Return collection of removed nodes.
     *
     * @param NodeCollection $nodeCollection
     * @param Node[] $nodes
     * @return NodeCollection
     */
    public function __invoke(NodeCollection $nodeCollection, Node ...$nodes): NodeCollection
    {
        foreach($nodeCollection as $k => $node)
        {
            foreach($nodes as $removeNode)
            {
                if($node->equals($removeNode))
                {
                    unset($nodeCollection[$k]);
                }
            }

            if(!$node->children()->isEmpty())
            {
                $this->__invoke($node->children(), ...$nodes);
            }
        }

        return $nodeCollection;
    }
}
