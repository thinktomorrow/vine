<?php

namespace Thinktomorrow\Vine\Commands;

use Thinktomorrow\Vine\Node;
use Thinktomorrow\Vine\NodeCollection;

class Copy
{
    /**
     * Keep in mind that Node::copy() utilises the Copy class when used with the depth parameter.
     * So here the Node::copy() must never be passed a param to avoid infinite call loop.
     *
     * @param Node $node
     * @param null|int $depth (null is infinite)
     *
     * @return Node
     */
    public function __invoke(Node $node, ?int $depth = null): Node
    {
        $copy = $node->copyIsolatedNode();

        return $copy->addChildNodes($this->recursiveDepth($node->getChildNodes(), $depth));
    }

    /**
     * @param NodeCollection $nodeCollection
     * @param null           $depth
     * @param int            $currentDepth
     *
     * @return NodeCollection
     */
    private function recursiveDepth(NodeCollection $nodeCollection, ?int $depth = null, int $currentDepth = 0)
    {
        $className = get_class($nodeCollection);

        if (! is_null($depth) && $depth <= $currentDepth) {
            return new $className();
        }

        $copyCollection = new $className();
        $currentDepth++;

        /** @var Node $node */
        foreach ($nodeCollection as $node) {
            $copyCollection->add($subNode = $node->copyIsolatedNode());

            $subNode->addChildNodes($this->recursiveDepth($node->getChildNodes(), $depth, $currentDepth));
        }

        return $copyCollection;
    }
}
