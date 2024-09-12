<?php

namespace Thinktomorrow\Vine\Commands;

use Thinktomorrow\Vine\Node;

class Move
{
    /**
     * Move a child node to a different parent.
     *
     * @param Node $node
     * @param Node $parent
     *
     * @return Node
     */
    public function __invoke(Node $node, Node $parent): Node
    {
        if ($node->equalsNode($parent)) {
            return $node;
        }

        if (! $node->isRootNode()) {
            $node->getParentNode()->removeNode($node);
        }

        $parent->addChildNodes($node);

        return $node;
    }
}
