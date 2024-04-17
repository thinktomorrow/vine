<?php

namespace Thinktomorrow\Vine;

class NodeCollectionFactory
{
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
    }

    private function findById($nodes, $id): ?Node
    {
        foreach($nodes as $node) {
            if($node->getNodeId() == $id) {
                return $node;
            }
        }

        return null;
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
}
