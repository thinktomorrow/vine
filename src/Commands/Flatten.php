<?php

namespace Thinktomorrow\Vine\Commands;

use Thinktomorrow\Vine\Node;
use Thinktomorrow\Vine\NodeCollection;

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

        /** @var Node $node */
        foreach ($nodeCollection as $k => $node) {
            $flattened[] = $node;

            if ($node->hasChildNodes()) {
                $flattened = array_merge($flattened, $this->__invoke($node->getChildNodes())->all());
            }
        }

        return new NodeCollection($flattened);
    }
}
