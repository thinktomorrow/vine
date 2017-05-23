<?php

namespace Vine\Commands;

use Vine\Node;
use Vine\NodeCollection;
use Vine\TreeFactory;

class Filter
{
    /**
     * Return collection of removed nodes?
     *
     * @param NodeCollection $nodeCollection
     * @return NodeCollection
     */
    public function __invoke(NodeCollection $nodeCollection, Callable $callback): NodeCollection
    {
        $flatten = (new Flatten())->__invoke($nodeCollection);

        $flattenArray = array_filter($flatten->all(),$callback);

        var_dump((new NodeCollection($flattenArray))->inflate());
        return (new NodeCollection($flattenArray))->inflate();

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
