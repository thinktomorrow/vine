<?php

namespace Thinktomorrow\Vine\Queries;

use Closure;
use Thinktomorrow\Vine\Node;
use Thinktomorrow\Vine\NodeCollection;

class FindFirst
{
    public function __invoke(NodeCollection $nodeCollection, string|Closure $key, ?array $values = null): ?Node
    {
        /** @var Node $node */
        foreach ($nodeCollection as $node) {
            if ($key instanceof Closure) {
                if (true === call_user_func($key, $node)) {
                    return $node;
                }
            } elseif (! is_null($values) && $node->hasNodeValue($key, $values)) {
                return $node;
            }

            if ($node->hasChildNodes()) {
                if ($childNode = $this->__invoke($node->getChildNodes(), $key, $values)) {
                    return $childNode;
                }
            }
        }

        return null;
    }
}
