<?php

namespace Vine\Commands;

use Vine\Node;
use Vine\NodeCollection;

class Collapse
{
    /**
     * Filter node tree and collapse them to a flat collection
     *
     * @param NodeCollection $nodeCollection
     * @param Node[] $nodes
     * @return NodeCollection
     */
    public function __invoke(NodeCollection $nodeCollection, Callable $callable): NodeCollection
    {
        foreach($nodeCollection as $k => $node)
        {
            $items = array_map($callback, $this->items, $keys);

            return new static(array_combine($keys, $items));

            foreach($nodes as $removeNode)
            {
                if($node->equals($removeNode))
                {
                    unset($nodeCollection[$k]);
                }
            }

            if(!$node->children()->isEmpty())
            {
                $this->__invoke($node->children(), ...$nodes);
            }
        }

        return $nodeCollection;
    }
}
