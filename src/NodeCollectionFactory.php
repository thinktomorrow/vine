<?php

namespace Vine;

use Vine\Transposers\Transposable;

class NodeCollectionFactory
{
    /**
     * Create a node collection in strict mode.
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
        $this->hydrate($transposable);

        $this->addOrphans();

        $this->identifyRootNodes();

        $this->structureCollection($transposable);

        return $this->roots;
    }

    private function hydrate(Transposable $transposable)
    {
        $id_key = $transposable->key();
        $parent_key = $transposable->ParentKey();

        foreach ($transposable->all() as $i => $entry) {

            $id = is_object($entry) ? $entry->{$id_key} : $entry[$id_key];
            $parentId = is_object($entry) ? $entry->{$parent_key} : $entry[$parent_key];

            $entryNode = ($entry instanceof Node) ? $entry : new Node($entry);

            // Keep track of flattened list of all nodes
            $this->index[$id] = $entryNode;

            // Add node to tree
            $this->addChild($parentId, $entryNode);
        }
    }

    /**
     * @param $parentId
     * @param $entryNode
     * @return mixed
     */
    private function addChild($parentId, $entryNode)
    {
        if (!$parentId) return;

        if (isset($this->index[$parentId])) {
            $this->index[$parentId]->addChildren([$entryNode]);
            return;
        }

        $this->catchOrphan($parentId, $entryNode);
    }

    /**
     * @param $parentId
     * @param $entryNode
     */
    private function catchOrphan($parentId, $entryNode)
    {
        if (!isset($this->orphans[$parentId])) {
            $this->orphans[$parentId] = new NodeCollection();
        }
        $this->orphans[$parentId][] = $entryNode;
    }

    /**
     * All orphans need to be assigned to their respective parents
     */
    private function addOrphans()
    {
        foreach ($this->orphans as $parentId => $orphans) {
            if (!isset($this->index[$parentId])) {

                // Strict check which means there is a node assigned to an non-existing parent
                if ($this->strict) {
                    throw new \LogicException('Parent reference to a non-existing node via identifier [' . $parentId . ']');
                }

                continue;
            }

            $this->index[$parentId]->addChildren($orphans->all());
        }
    }

    private function identifyRootNodes()
    {
        foreach ($this->index as $node) {
            if ($node->isRoot()) {
                $this->roots[] = $node;
            }
        }
    }

    /**
     * @param Transposable $transposable
     */
    private function structureCollection(Transposable $transposable)
    {
        // At this point we allow to alter each entry.
        // Useful to add values depending on the node structure
        if (method_exists($transposable, 'entry')) {
            foreach ($this->index as $node) {
                $node->replaceEntry($transposable->entry($node));
            }
        }

        // At this point we will sort all children should the transposer has set a key to sort on
        if (property_exists($transposable, 'sortChildrenBy')) {
            foreach ($this->index as $node) {
                $node->sort($transposable->sortChildrenBy);
            }
        }
    }
}
