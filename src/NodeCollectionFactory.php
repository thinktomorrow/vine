<?php

namespace Thinktomorrow\Vine;

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

//    public function fromSource(Source $source)
    public function fromIterable(iterable $items, callable $createNode)
    {
        $nodes = $this->mapIterable($items, $createNode);

        $nodeCollection = new NodeCollection();

        /** @var Node $node */
        foreach($nodes as $node) {
            if($node->getParentNodeId()) {
                $node->moveToParentNode(
                    $this->findById($nodes, $node->getParentNodeId())
                );
            } else {
                $nodeCollection->add($node);
            }
        }

        return $nodeCollection;
//
//
//        dd($nodes);
//
//        // Keep an index reference of all nodes - since these are objects, the
//        // index points to the same object as the node in the nodeCollection
//        $nodes = $this->mapIterable($nodes, function(Node $node, $key) {
//        });
//
//        // generate iterable to array map
//
//        //
//
////        $nodes = iterator_to_array($source, function ($entry) use ($id_key, $parent_key) {
////            return $entry instanceof NodeSource ? $entry->toArray() : $entry;
////        });
//        // Create nodes of each entry
//
//        // if child node, add as child to parent node
//
//        // only return the root items
//
//        $this->hydrate($source);
//
//        $this->addOrphans();
//
//        $this->identifyRootNodes();
//
//        $this->structureCollection($source);
//
//        return $this->nodeCollection;
    }

    private function findById($nodes, $id): ?Node
    {
        foreach($nodes as $node) {
            if($node->getNodeId() == $id) {
                return $node;
            }
        }
    }

    /** @return Node[] */
    private function mapIterable(iterable $items, callable $callback): array
    {
        $result = [];

        foreach ($items as $key => $item) {
            $result[$key] = $callback($item, $key);

            if (! $result[$key] instanceof Node) {
                throw new \InvalidArgumentException('The create callback must return a Node instance.');
            }
        }

        return $result;
    }

//    private function hydrate(Source $source)
    private function hydrate(iterable $entries, string $id_key = 'id', string $parent_key = 'parent_id')
    {
//        $id_key = method_exists($source, 'nodeKeyIdentifier') ? $source->nodeKeyIdentifier() : 'id';
//        $parent_key = method_exists($source, 'nodeParentKeyIdentifier') ? $source->nodeParentKeyIdentifier() : 'parent_id';

        foreach ($source->nodeEntries() as $entry) {
            $id = $entry instanceof NodeSource ? $entry->getNodeId() : $entry[$id_key];
            $parentId = $entry instanceof NodeSource ? $entry->getParentNodeId() : $entry[$parent_key];

            $node = $source->createNode($entry);

            // Keep track of flattened list of all nodes
            $this->index[$id] = $node;

            // Add node to tree
            $this->addChild($parentId, $node);
        }
    }

    /**
     * @param string|int $parentId
     * @param mixed      $entryNode
     */
    private function addChild($parentId, $entryNode)
    {
        if (! $parentId) {
            return;
        }

        if (isset($this->index[$parentId])) {
            $this->index[$parentId]->addChildNodes([$entryNode]);

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
        if (! isset($this->orphans[$parentId])) {
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
            if (! isset($this->index[$parentId])) {
                // Strict check which means there is a node assigned to an non-existing parent
                if ($this->strict) {
                    throw new \LogicException('Parent reference to a non-existing node via identifier ['.$parentId.']');
                }

                continue;
            }

            $this->index[$parentId]->addChildNodes($orphans->all());
        }

        unset($this->orphans);
    }

    private function identifyRootNodes()
    {
        /** @var Node $node */
        foreach ($this->index as $node) {
            if ($node->isRootNode()) {
                $this->nodeCollection[] = $node;
            }
        }
    }

    private function structureCollection(Source $source)
    {
        // At this point we allow to alter each entry.
        // Useful to add values depending on the node structure
        if (method_exists($source, 'entry')) {
            /** @var Node $node */
            foreach ($this->index as $node) {
                $node->replaceNodeEntry($source->entry($node));
            }
        }

        // At this point we will sort all children should the source has set a key to sort on
        // TODO: this can be done via sort method on tree itself
        if (property_exists($source, 'sortChildrenBy')) {
            foreach ($this->index as $node) {
                $node->sortChildNodes($source->sortChildrenBy);
            }
        }
    }
}
