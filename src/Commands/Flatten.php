<?php

namespace Vine\Commands;

use Vine\Node;
use Vine\NodeCollection;

class Flatten
{
    /**
     * Return flattened collection
     *
     * @param NodeCollection $nodeCollection
     * @return NodeCollection
     */
    public function __invoke(NodeCollection $nodeCollection): NodeCollection
    {
        $flattened = [];

        foreach($nodeCollection as $k => $node)
        {
            $flattened[] = $node;

            if(!$node->children()->isEmpty())
            {
                $flattened = array_merge($flattened, $this->__invoke($node->children())->all());
            }
        }

        return new NodeCollection(...$flattened);
    }
}
