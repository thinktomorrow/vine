<?php

namespace Vine;

use Vine\Transposable;

class TreeFactory
{
    /**
     * Create tree in strict mode.
     * This will throw exception if index contains invalid references.
     * e.g. parent reference to non-existing node
     *
     * @var bool
     */
    private $strict = false;

    private $index;
    private $orphans;
    private $roots;

    public function __construct()
    {
        $this->index = new NodeCollection();
        $this->roots = new NodeCollection();
        $this->orphans = [];
    }

    /**
     * @param bool $strict
     * @return $this
     */
    public function strict($strict = true)
    {
        $this->strict = !!$strict;

        return $this;
    }

    public function create(Transposable $transposable)
    {
        foreach($transposable->all() as $i => $entry)
        {
            $id = is_object($entry) ? $entry->{$transposable->key()}: $entry[$transposable->key()];
            $parentId = is_object($entry) ? $entry->{$transposable->parentKey()}: $entry[$transposable->parentKey()];

            $entryNode = new Node($entry);

            $this->index[$id] = $entryNode;
            $this->addAsChild($parentId, $entryNode);
            $this->addAsRoot($parentId, $entryNode);
        }

        // All orphans need to be assigned to their respective parents
        foreach($this->orphans as $parentId => $orphans)
        {
            if(!isset($this->index[$parentId]))
            {
                // Strict check which means there is a node assigned to an non-existing parent
                if($this->strict)
                {
                    throw new \LogicException('Parent reference to a non-existing node via identifier ['.$parentId.']');
                }

                continue;
            }

            $this->index[$parentId]->addChildren($orphans->all());
        }

        // At this point we allow to alter each entry.
        // Useful to add values depending on the node structure
        if(method_exists($transposable,'entry'))
        {
            foreach($this->index as $node)
            {
                $node->replaceEntry($transposable->entry($node));
            }
        }

        // At this point we will sort all children should the transposer has set a key to sort on
        if(property_exists($transposable, 'sortChildrenBy'))
        {
            foreach($this->index as $node)
            {
                $node->sort($transposable->sortChildrenBy);
            }
        }

        // Collect all root nodes because they contain the entire tree
        return new Tree($this->roots, $this->index);
    }

    /**
     * @param $parentId
     * @param $entryNode
     * @return mixed
     */
    private function addAsChild($parentId, $entryNode)
    {
        if (!$parentId) return;

        if (isset($this->index[$parentId])) {
            $this->index[$parentId]->addChildren([$entryNode]);
            return;
        }

        $this->addOrphan($parentId, $entryNode);
    }

    /**
     * @param $parentId
     * @param $entryNode
     * @return mixed
     */
    private function addAsRoot($parentId, $entryNode)
    {
        if ($parentId) return;

        $this->roots[] = $entryNode;
    }

    /**
     * @param $parentId
     * @param $entryNode
     */
    private function addOrphan($parentId, $entryNode)
    {
        if (!isset($this->orphans[$parentId])) {
            $this->orphans[$parentId] = new NodeCollection();
        }
        $this->orphans[$parentId][] = $entryNode;
    }
}
