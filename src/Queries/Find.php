<?php

namespace Thinktomorrow\Vine\Queries;

use Closure;
use Thinktomorrow\Vine\Node;
use Thinktomorrow\Vine\NodeCollection;

class Find
{
    /**
     * @param NodeCollection $nodeCollection
     * @param string|Closure $key
     * @param array $values
     * @return NodeCollection
     */
    public function __invoke(NodeCollection $nodeCollection, $key, array $values): NodeCollection
    {
        $nodes = new NodeCollection();

        /** @var Node $node */
        foreach ($nodeCollection as $node) {
            if ($key instanceof Closure) {
                if (true === call_user_func($key, $node)) {
                    $nodes->add($node);
                }
            } elseif (! is_null($values) && $node->hasNodeEntryValue($key, $values)) {
                $nodes->add($node);
            }

            if ($node->hasChildNodes()) {
                $nodes->merge($this->__invoke($node->getChildNodes(), $key, $values));
            }
        }

        return $nodes;
    }
}
