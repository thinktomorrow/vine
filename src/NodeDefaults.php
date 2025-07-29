<?php

namespace Thinktomorrow\Vine;

use Thinktomorrow\Vine\Commands\Copy;
use Thinktomorrow\Vine\Commands\Move;
use Thinktomorrow\Vine\Queries\Ancestors;
use Thinktomorrow\Vine\Queries\Count;
use Thinktomorrow\Vine\Queries\Pluck;

trait NodeDefaults
{
    protected ?Node $parentNode = null;
    protected NodeCollection $children;

    protected function getNodeIdKey(): string
    {
        return 'id';
    }

    protected function getParentNodeIdKey(): string
    {
        return 'parent_id';
    }

    public function getNodeId(): string
    {
        return (string) $this->{$this->getNodeIdKey()};
    }

    public function getParentNodeId(): ?string
    {
        if ($this->parentNode) {
            return $this->parentNode->getNodeId();
        }

        if ($parentId = $this->{$this->getParentNodeIdKey()}) {
            return (string) $parentId;
        }

        return null;
    }

    public function equalsNode(Node $other): bool
    {
        return $this === $other;
    }

    public function addChildNodes($children): Node
    {
        $children = $this->transformToNodeCollection($children);

        $this->children->merge($children);

        array_map(function (Node $child) {
            $child->setParentNode($this);
        }, $children->all());

        return $this;
    }

    public function getChildNodes(): NodeCollection
    {
        return $this->children;
    }

    public function hasChildNodes(): bool
    {
        return ! $this->children->isEmpty();
    }

    public function sortChildNodes($key): Node
    {
        if ($this->hasChildNodes()) {
            $this->children = $this->children->sort($key);
        }

        return $this;
    }

    public function hasNodeValue($key, $value): bool
    {
        return in_array($this->getNodeValue($key), (array) $value);
    }

    public function getNodeValue($key, $default = null): mixed
    {
        return $this->{$key} ?? $default;
    }

    public function getSortValue($key)
    {
        return $this->{$key};
    }

    public function getParentNode(): ?Node
    {
        return $this->parentNode;
    }

    public function setParentNode(Node $parentNode): Node
    {
        $this->parentNode = $parentNode;

        return $this;
    }

    public function hasParentNode(): bool
    {
        return ! ! $this->parentNode;
    }

    public function getSiblingNodes(): NodeCollection
    {
        $siblingNodes = $this->emptyNodeCollection();

        if ($this->isRootNode()) {
            return $siblingNodes;
        }

        foreach ($this->getParentNode()->getChildNodes() as $childNode) {
            if ($childNode->equalsNode($this)) {
                continue;
            }
            $siblingNodes->add($childNode);
        }

        return $siblingNodes;
    }

    public function hasSiblingNodes(): bool
    {
        return ! $this->getSiblingNodes()->isEmpty();
    }

    public function getLeftSiblingNode(): ?Node
    {
        if ($this->isRootNode()) {
            return null;
        }

        $childNodes = $this->getParentNode()->getChildNodes();

        foreach ($childNodes as $i => $siblingNode) {
            if ($siblingNode->equalsNode($this) && isset($childNodes[$i - 1])) {
                return $childNodes[$i - 1];
            };
        }

        return null;
    }

    public function getRightSiblingNode(): ?Node
    {
        if ($this->isRootNode()) {
            return null;
        }

        $childNodes = $this->getParentNode()->getChildNodes();

        foreach ($childNodes as $i => $siblingNode) {
            if ($siblingNode->equalsNode($this) && isset($childNodes[$i + 1])) {
                return $childNodes[$i + 1];
            };
        }

        return null;
    }

    public function removeNode(Node $node): Node
    {
        $node->removeParentNode();
        $this->getChildNodes()->removeNode($node);

        return $this;
    }

    public function removeParentNode(): Node
    {
        $this->parentNode = null;

        return $this;
    }

    /**
     * Move a child node to a different parent.
     *
     * @param Node $parentNode
     *
     * @return mixed
     */
    public function moveToParentNode(Node $parentNode)
    {
        return (new Move())($this, $parentNode);
    }

    public function moveNodeToRoot()
    {
        if (! $this->isRootNode()) {
            $this->getParentNode()->removeNode($this);
            $this->parentNode = null;
        }
    }

    /**
     * At which depth does this node resides inside the entire tree.
     *
     * @return int
     */
    public function getNodeDepth(): int
    {
        if ($this->isRootNode()) {
            return 0;
        }

        return $this->getParentNode()->getNodeDepth() + 1;
    }

    /**
     * count of all direct child nodes.
     *
     * @return int
     */
    public function getChildNodesCount(): int
    {
        if ($this->isLeafNode()) {
            return 0;
        }

        return $this->getChildNodes()->count();
    }

    /**
     * Total of all child nodes.
     *
     * @return int
     */
    public function getTotalChildNodesCount(): int
    {
        if ($this->isLeafNode()) {
            return 0;
        }

        return (new Count())($this);
    }

    /**
     * @return bool
     */
    public function isLeafNode(): bool
    {
        return $this->children->isEmpty();
    }

    /**
     * @return bool
     */
    public function isRootNode(): bool
    {
        return ! $this->hasParentNode();
    }

    /**
     * @param string|int $key
     * @param array      $values
     *
     * @return NodeCollection
     */
    public function findChildNodes($key, array $values): NodeCollection
    {
        return $this->getChildNodes()->findMany($key, $values);
    }

    /**
     * @param string|int $key
     * @param mixed      $value
     *
     * @return Node
     */
    public function findChildNode($key, $value): Node
    {
        return $this->getChildNodes()->find($key, $value);
    }

    public function getRootNode(): Node
    {
        if ($this->isRootNode()) {
            return $this;
        }

        return $this->getAncestorNodes()->first();
    }

    /**
     * @param int $depth
     *
     * @return NodeCollection
     */
    public function getAncestorNodes($depth = null): NodeCollection
    {
        return (new Ancestors())($this, $depth);
    }

    /**
     * Get flat array of plucked values from child nodes.
     *
     * @param string|int      $key
     * @param string|int|null $value
     *
     * @return array
     */
    public function pluckChildNodes($key, $value = null, bool $includeSelf = false): array
    {
        $output = $this->pluck($key, $value);

        if (! $includeSelf) {
            if (array_is_list($output)) {
                array_shift($output);
            } else {
                unset($output[array_key_first($output)]);
            }
        }

        return $output;
    }

    /**
     * Get flat array of plucked values from child nodes.
     *
     * @param string $key
     * @param null $value
     *
     * @return array
     */
    public function pluckAncestorNodes(string $key, $value = null, bool $includeSelf = false): array
    {
        $output = $this->pluck($key, $value, false);

        if (! $includeSelf) {
            if (array_is_list($output)) {
                array_shift($output);
            } else {
                unset($output[array_key_first($output)]);
            }
        }

        return $output;
    }

    private function pluck($key, $value = null, bool $down = true): array
    {
        return (new Pluck())($this, $key, $value, $down);
    }

    /**
     * Get a copy of this node.
     *
     * @param int|null $depth
     *
     * @return Node
     */
    public function copyNode($depth = null): Node
    {
        return $depth === 0
            ? $this->copyIsolatedNode()
            : (new Copy())($this, $depth);
    }

    /**
     * Copy of this node without its parent / children relationships.
     *
     * @return Node
     */
    public function copyIsolatedNode(): Node
    {
        $copy = clone $this;

        $className = get_class($this->getChildNodes());
        $copy->children = new $className();
        $copy->parentNode = null;

        return $copy;
    }

    /**
     * Reduce collection to the nodes that pass the callback
     * Shaking a collection will keep the ancestor structure.
     *
     * @param callable $callback
     */
    public function shakeChildNodes(callable $callback): Node
    {
        $node = $this->copyIsolatedNode();

        return $this->isLeafNode() ? $node : $node->addChildNodes($this->getChildNodes()->shake($callback));
    }

    /**
     * Same as shaking except that it will not keep the ancestor structure.
     */
    public function pruneChildNodes(callable $callback): Node
    {
        $node = $this->copyIsolatedNode();

        return $this->isLeafNode() ? $node : $node->addChildNodes($this->getChildNodes()->prune($callback));
    }

    /**
     * If the user has given a custom node collection, we'll want to
     * honour this and keep this class as the node collection class
     *
     * @return NodeCollection
     */
    protected function emptyNodeCollection(array $children = []): NodeCollection
    {
        $className = get_class($this->getChildNodes());

        return new $className($children);
    }

    /**
     * @param array|Node|NodeCollection $children
     *
     * @return NodeCollection
     */
    private function transformToNodeCollection($children): NodeCollection
    {
        if (is_array($children)) {
            $children = $this->emptyNodeCollection($children);
        } elseif ($children instanceof Node) {
            $children = $this->emptyNodeCollection([$children]);
        } elseif (! $children instanceof NodeCollection) {
            throw new \InvalidArgumentException('Invalid children parameter. Accepted types are array or NodeCollection.');
        }

        return $children;
    }
}
