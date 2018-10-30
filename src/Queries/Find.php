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
            if (in_array($node->entry($key), $values)) {
                $nodes->add($node);
            }

            if (!$node->children()->isEmpty()) {
                $nodes->merge($this->__invoke($node->children(), $key, $values));
            }
        }

        return $nodes;
    }
}
