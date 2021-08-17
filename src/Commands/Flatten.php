<?php

namespace Vine\Commands;

use Vine\NodeCollection;

class Flatten
{
    /**
     * Return flattened collection.
     *
     * @param NodeCollection $nodeCollection
     *
     * @return NodeCollection
     */
    public function __invoke(NodeCollection $nodeCollection): NodeCollection
    {
        $flattened = [];

        foreach ($nodeCollection as $k => $node) {
            $flattened[] = $node;

            if (!$node->getChildren()->isEmpty()) {
                $flattened = array_merge($flattened, $this->__invoke($node->getChildren())->all());
            }
        }

        return new NodeCollection(...$flattened);
    }
}
