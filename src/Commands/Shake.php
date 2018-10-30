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
     * @param NodeCollection $nodeCollection
     * @param callable       $callback
     * @param bool           $prune
     *
     * @return NodeCollection
     */
    public function __invoke(NodeCollection $nodeCollection, callable $callback, $prune = false): NodeCollection
    {
        $copiedNodeCollection = $nodeCollection->copy();

        $shakedChildren = (new Slice())($copiedNodeCollection, ...$this->getBlacklistedNodes($copiedNodeCollection, $callback));

        return $shakedChildren;
    }

    /**
     * Blacklist of allowed nodes - we reverse the callback so we get the nodes that we do not want included.
     *
     * @param $copiedNodeCollection
     * @param callable $callback
     *
     * @return array
     */
    private function getBlacklistedNodes(NodeCollection $copiedNodeCollection, callable $callback): array
    {
        $flatten = (new Flatten())($copiedNodeCollection);

        $whitelistedNodes = new NodeCollection(...array_filter($flatten->all(), $callback));

        foreach ($whitelistedNodes as $node) {
            $whitelistedNodes->merge($node->ancestors());
        }

        return array_filter($flatten->all(), function (Node $node) use ($whitelistedNodes) {
            // Todo we should make this check optimized for performance
            foreach ($whitelistedNodes as $whitelistedNode) {
                if ($node->equals($whitelistedNode)) {
                    return false;
                }
            }

            return true;
        });
    }
}
