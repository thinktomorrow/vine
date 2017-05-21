<?php

namespace Vine;

/**
 * Class Tree
 * Tree collection is exactly the same as a node collection with the difference
 * that a tree contains a flat index reference to each node, based on the primary key.
 * This allows for fast retrieval of nodes by searching on their primary key.
 *
 * @package Vine
 */
class Tree extends NodeCollection
{
    /**
     * Flat listing of nodes with primary id as collection key.
     *
     * @var NodeCollection
     */
    private $index;

    public function __construct(NodeCollection $nodeCollection, NodeCollection $index)
    {
        $this->nodes = $nodeCollection->all();
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
