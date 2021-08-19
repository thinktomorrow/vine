<?php

namespace Thinktomorrow\Vine\Commands;

use Thinktomorrow\Vine\Node;
use Thinktomorrow\Vine\NodeCollection;

class Prune
{
    /**
     * Prune a node collection by a callback so only the filtered nodes are left as children.
     * Pruning a collection only keeps the filtered nodes and collapses the ancestor tree.
     * Shaking a collection retains the ancestors for each filtered node.
     *
     * @param NodeCollection $nodeCollection
     * @param callable       $callback
     *
     * @return NodeCollection
     *
     * @internal param Node[] $nodes
     */
    public function __invoke(NodeCollection $nodeCollection, callable $callback): NodeCollection
    {
        $copiedNodeCollection = $nodeCollection->copy();

        $prunedChildren = (new Slice())($copiedNodeCollection, ...$this->getBlacklistedNodes($copiedNodeCollection, $callback));

        return $prunedChildren;
    }

    /**
     * Blacklist of disallowed nodes
     * Note: the passed callback determines the nodes which should be kept but here
     * we reverse the callback so we get the nodes that need to be excluded.
     *
     * @param NodeCollection $copiedNodeCollection
     * @param callable       $callback
     *
     * @return array
     */
    private function getBlacklistedNodes(NodeCollection $copiedNodeCollection, callable $callback): array
    {
        $flatten = (new Flatten())($copiedNodeCollection);

        return array_filter($flatten->all(), function (Node $node) use ($callback) {
            return ! $callback($node);
        });
    }
}
