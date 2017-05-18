<?php

namespace Vine\Queries;

use Vine\Node;
use Vine\NodeCollection;

class Count
{
    /**
     * @param Node $node
     * @return int
     */
    public function __invoke(Node $node): int
    {
        return $this->recursiveCount($node);
    }

    /**
     * Count of all the children nodes
     *
     * @param Node $node
     * @return int
     */
    private function recursiveCount(Node $node): int
    {
        $count = 0;

        foreach($node->children() as $child)
        {
            $count++; // Childnode itself
            $count += $this->recursiveCount($child); // Children of childnode
        }

        return $count;
    }
}
