<?php

namespace Thinktomorrow\Vine\Queries;

use Thinktomorrow\Vine\Node;
use Thinktomorrow\Vine\DefaultNode;
use Thinktomorrow\Vine\NodeCollection;

class FindFirst
{
    /**
     * @param NodeCollection $nodeCollection
     * @param $key
     * @param array $values
     *
     * @return DefaultNode
     */
    public function __invoke(NodeCollection $nodeCollection, $key, array $values = null): ?DefaultNode
    {
        /** @var Node $node */
        foreach ($nodeCollection as $node) {

            if($key instanceof \Closure) {
                if(true === call_user_func($key, $node)) {
                    return $node;
                }
            } else if (!is_null($values) && $node->hasNodeEntryValue($key, $values)) {
                return $node;
            }

            if ($node->hasChildNodes()) {
                if($childNode = $this->__invoke($node->getChildNodes(), $key, $values)){
                    return $childNode;
                }
            }
        }

        return null;
    }
}
