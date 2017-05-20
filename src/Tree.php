<?php

namespace Vine;

class Tree extends NodeCollection
{
    /**
     * @var NodeCollection
     */
    private $index;

    public function __construct(NodeCollection $nodes, NodeCollection $index)
    {
        $this->nodes = $nodes;
        $this->index = $index;
    }

    /**
     * Count of all nodes
     *
     * @return int
     */
    public function count()
    {
        return $this->index->count();
    }

    /**
     * @param $id
     * @return null|Node
     */
    public function findByIndex($id)
    {
        return $this->findManyByIndex((array)$id)->first();
    }

    public function findManyByIndex(array $ids): NodeCollection
    {
        $nodes = new NodeCollection;

        foreach($ids as $id)
        {
            if(!isset($this->index[$id])) continue;
            $nodes->add($this->index[$id]);
        }

        return $nodes;
    }
}
