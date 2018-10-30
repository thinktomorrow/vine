<?php

namespace Vine\Commands;

use Vine\NodeCollection;

class Inflate
{
    /**
     * Return proper tree structure of flattened node collection.
     * Nodes from a flat collection retain their relations so
     * we just need to return the root nodes.
     *
     * @param NodeCollection $flatCollection
     *
     * @return NodeCollection
     */
    public function __invoke(NodeCollection $flatCollection): NodeCollection
    {
        $roots = new NodeCollection();

        foreach ($flatCollection as $k => $node) {
            if ($node->isRoot()) {
                $roots->add($node);
            }
        }

        return $roots;
    }
}
