<?php

namespace Vine\Queries;

use Vine\NodeCollection;

class Find
{
    /**
     * @param NodeCollection $nodeCollection
     * @param $key
     * @param array $values
     *
     * @return NodeCollection
     */
    public function __invoke(NodeCollection $nodeCollection, $key, array $values): NodeCollection
    {
        $nodes = new NodeCollection();

        foreach ($nodeCollection as $node) {
            if ($node->has($key, $values)) {
                $nodes->add($node);
            }

            if ($node->hasChildren()) {
                $nodes->merge($this->__invoke($node->getChildren(), $key, $values));
            }
        }

        return $nodes;
    }
}
