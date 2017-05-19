<?php

namespace Vine\Queries;

use Vine\Node;
use Vine\NodeCollection;

class Find
{
    /**
     * @param Node $node
     * @param $key
     * @param null $value
     * @return NodeCollection
     */
    public function __invoke(Node $node, $key, array $values): NodeCollection
    {
        $nodes = new NodeCollection;

        foreach($node->children() as $node)
        {
            if(in_array($node->entry($key), $values))
            {
                $nodes->add($node);
            }

            $nodes->merge($this->__invoke($node, $key, $values));
        }

        return $nodes;
    }
}
