<?php

namespace Vine\Commands;

use Vine\Node;
use Vine\NodeCollection;

class Shake
{
    /**
     * Shake node collection by a callback so only the filtered nodes are left as children with respect to their ancestor relations.
     * Shaking a collection retains the ancestors for each filtered node
     * Pruning a collection only keeps the filtered nodes and collapses the ancestor tree.
     *
     * @param Node $node
     * @param callable $callback
     * @param bool $prune -
     * @return Node
     * @internal param Node[] $nodes
     */
    public function __invoke(Node $node, Callable $callback, $prune = false): Node
    {
        $shakedNode = $node->isolatedCopy();
        $copiedNode = $node->copy();

        $shakedChildren = (new Slice())($copiedNode->children(), ...$this->getBlacklistedNodes($copiedNode, $callback));

        return $shakedNode->addChildren($shakedChildren);
    }

    /**
     * Blacklist of allowed nodes - we reverse the callback so we get the nodes that we do not want included
     * @param $copiedNode
     * @param callable $callback
     * @return array
     */
    private function getBlacklistedNodes(Node $copiedNode, Callable $callback): array
    {
        $flatten = (new Flatten())($copiedNode->children());

        $whitelistedNodes = new NodeCollection(...array_filter($flatten->all(),$callback));

        foreach($whitelistedNodes as $node)
        {
            $whitelistedNodes->merge($node->ancestors());
        }

        return array_filter($flatten->all(), function (Node $node) use ($whitelistedNodes)
        {
            // Todo we should make this check optimized for performance
            foreach($whitelistedNodes as $whitelistedNode)
            {
                if($node->equals($whitelistedNode))
                {
                    return false;
                }
            }

            return true;
        });
    }
}
