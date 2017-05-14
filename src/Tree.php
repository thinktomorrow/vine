<?php

namespace Vine;

class Tree
{
    /**
     * NodeCollection
     * @var Node
     */
    private $roots;

    /**
     * @var NodeCollection
     */
    private $index;

    public function __construct(NodeCollection $roots, NodeCollection $index)
    {
        $this->roots = $roots;
        $this->index = $index;
    }

    public function count()
    {
        return $this->index->count();
    }

    public function roots(): NodeCollection
    {
        return $this->roots;
    }

    public function find($id): Node
    {
        return $this->findMany((array)$id)->first();
    }

    public function findMany(array $ids): NodeCollection
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