<?php

namespace Vine\Queries;

use Vine\Node;
use Vine\NodeCollection;

class FindFirst
{
    /**
     * @param NodeCollection $nodeCollection
     * @param $key
     * @param array $values
     *
     * @return null|Node
     */
    public function __invoke(NodeCollection $nodeCollection, $key, array $values): ?Node
    {
        foreach ($nodeCollection as $node) {
            if ($node->has($key, $values)) {
                return $node;
            }

            if ($node->hasChildren()) {
                if ($childNode = $this->__invoke($node->children(), $key, $values)) {
                    return $childNode;
                }
            }
        }

        return null;
    }
}
