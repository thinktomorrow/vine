<?php

namespace Vine;

class NodeCollectionFactory
{
    /**
     * Create a node collection in strict mode.
     * This will throw an exception if the index build contains invalid
     * references, like referencing to a parent node that doesn't exist.
     *
     * @var bool
     */
    private $strict = false;

    /**
     * The resulting node collection build up from the source data.
     *
     * @var NodeCollection
     */
    private $nodeCollection;

    private $index;
    private $orphans;

    public function __construct()
    {
        $this->nodeCollection = new NodeCollection();
        $this->index = new NodeCollection();
        $this->orphans = [];
    }

    /**
     * @param bool $strict
     *
     * @return $this
     */
    public function strict($strict = true)
    {
        $this->strict = (bool) $strict;

        return $this;
    }

    public function fromSource(Source $source)
    {
        $this->hydrate($source);

        $this->addOrphans();

        $this->identifyRootNodes();

        $this->structureCollection($source);

        return $this->nodeCollection;
    }

    private function hydrate(Source $source)
    {
        $id_key = $source->nodeKeyIdentifier();
        $parent_key = $source->nodeParentKeyIdentifier();

        foreach ($source->nodeEntries() as $i => $entry) {
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
     * @param string|int $parentId
     * @param mixed      $entryNode
     */
    private function addChild($parentId, $entryNode)
    {
        if (!$parentId) {
            return;
        }

        if (isset($this->index[$parentId])) {
            $this->index[$parentId]->addChildren([$entryNode]);

            return;
        }

        $this->catchOrphan($parentId, $entryNode);
    }

    /**
     * @param string|int $parentId
     * @param mixed      $entryNode
     */
    private function catchOrphan($parentId, $entryNode)
    {
        if (!isset($this->orphans[$parentId])) {
            $this->orphans[$parentId] = new NodeCollection();
        }
        $this->orphans[$parentId][] = $entryNode;
    }

    /**
     * All orphans need to be assigned to their respective parents.
     */
    private function addOrphans()
    {
        foreach ($this->orphans as $parentId => $orphans) {
            if (!isset($this->index[$parentId])) {

                // Strict check which means there is a node assigned to an non-existing parent
                if ($this->strict) {
                    throw new \LogicException('Parent reference to a non-existing node via identifier ['.$parentId.']');
                }

                continue;
            }

            $this->index[$parentId]->addChildren($orphans->all());
        }

        unset($this->orphans);
    }

    private function identifyRootNodes()
    {
        foreach ($this->index as $node) {
            if ($node->isRoot()) {
                $this->nodeCollection[] = $node;
            }
        }
    }

    private function structureCollection(Source $source)
    {
        // At this point we allow to alter each entry.
        // Useful to add values depending on the node structure
        if (method_exists($source, 'entry')) {
            foreach ($this->index as $node) {
                $node->replaceEntry($source->entry($node));
            }
        }

        // At this point we will sort all children should the transposer has set a key to sort on
        if (property_exists($source, 'sortChildrenBy')) {
            foreach ($this->index as $node) {
                $node->sort($source->sortChildrenBy);
            }
        }
    }
}
