<?php

namespace Thinktomorrow\Vine\Commands;

use Thinktomorrow\Vine\Node;
use Thinktomorrow\Vine\NodeCollection;

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
        $className = get_class($flatCollection);
        $roots = new $className;

        /** @var Node $node */
        foreach ($flatCollection as $k => $node) {
            if ($node->isRootNode()) {
                $roots->add($node);
            }
        }

        return $roots;
    }
}
