<?php

namespace Thinktomorrow\Vine\Queries;

use Thinktomorrow\Vine\Node;
use Thinktomorrow\Vine\NodeCollection;

class Ancestors
{
    /**
     * @param Node $node
     * @param int|null $depth
     *
     * @return NodeCollection
     */
    public function __invoke(Node $node, ?int $depth = null): NodeCollection
    {
        $ancestors = new NodeCollection();
        $currentDepth = 0;

        while ($parent = $node->getParentNode()) {
            if (! is_null($depth) && $currentDepth >= $depth) {
                break;
            }
            $currentDepth++;

            $node = $parent;
            $ancestors->add($parent);
        }

        return new NodeCollection(array_reverse($ancestors->all()));
    }
}
