<?php

namespace Vine\Commands;

use Vine\Node;

class Prune
{
    /**
     * Prune a node collection by a callback so only the filtered nodes are left as children.
     * Pruning a collection only keeps the filtered nodes and collapses the ancestor tree.
     * Shaking a collection retains the ancestors for each filtered node
     *
     * @param Node $node
     * @param callable $callback
     * @return Node
     * @internal param Node[] $nodes
     */
    public function __invoke(Node $node, Callable $callback): Node
    {
        $prunedNode = $node->isolatedCopy();
        $copiedNode = $node->copy();

        $prunedChildren = (new Slice())($copiedNode->children(), ...$this->getBlacklistedNodes($copiedNode, $callback));

        return $prunedNode->addChildren($prunedChildren);
    }

    /**
     * Blacklist of disallowed nodes
     * Note: the passed callback determines the nodes which should be kept but here
     * we reverse the callback so we get the nodes that need to be excluded
     *
     * @param $copiedNode
     * @param callable $callback
     * @return array
     */
    private function getBlacklistedNodes(Node $copiedNode, Callable $callback): array
    {
        $flatten = (new Flatten())($copiedNode->children());

        return array_filter($flatten->all(), function (Node $node) use ($callback)
        {
            return !$callback($node);
        });
    }
}
