<?php

namespace Thinktomorrow\Vine\Commands;

use Thinktomorrow\Vine\Node;
use Thinktomorrow\Vine\NodeCollection;

class Remove
{
    /**
     * Return collection of removed nodes.
     *
     * @param NodeCollection $nodeCollection
     * @param Node ...$nodes
     * @return NodeCollection
     */
    public function __invoke(NodeCollection $nodeCollection, Node ...$nodes): NodeCollection
    {
        /** @var Node $node */
        foreach ($nodeCollection as $k => $node) {
            foreach ($nodes as $nodeToBeRemoved) {
                if ($node->equalsNode($nodeToBeRemoved)) {
                    unset($nodeCollection[$k]);
                }
            }

            if ($node->hasChildNodes()) {
                $this->__invoke($node->getChildNodes(), ...$nodes);
            }
        }

        return $nodeCollection;
    }
}
