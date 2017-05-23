<?php

namespace Vine\Commands;

use Vine\Node;
use Vine\NodeCollection;
use Vine\TreeFactory;

class Filter
{
    /**
     * Filter nodes by callback
     *
     * @param NodeCollection $nodeCollection
     * @param callable $callback
     * @return NodeCollection
     */
    public function __invoke(NodeCollection $nodeCollection, Callable $callback): NodeCollection
    {
        $flatten = (new Flatten())->__invoke($nodeCollection);

        $flattenArray = array_filter($flatten->all(),$callback);
var_dump($flattenArray);
die();
        return (new NodeCollection(...$flattenArray))->inflate();
    }
}
