<?php

namespace Thinktomorrow\Vine\Queries;

use Closure;
use Thinktomorrow\Vine\Node;
use Thinktomorrow\Vine\NodeCollection;

class Find
{
    public function __invoke(NodeCollection $nodeCollection, string|Closure $key, ?array $values = null): NodeCollection
    {
        $className = get_class($nodeCollection);
        $nodes = new $className();

        /** @var Node $node */
        foreach ($nodeCollection as $node) {
            if ($key instanceof Closure) {
                if (true === call_user_func($key, $node)) {
                    $nodes->add($node);
                }
            } elseif (! is_null($values) && $node->hasNodeValue($key, $values)) {
                $nodes->add($node);
            }

            if ($node->hasChildNodes()) {
                $nodes->merge($this->__invoke($node->getChildNodes(), $key, $values));
            }
        }

        return $nodes;
    }
}
