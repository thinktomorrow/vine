<?php
declare(strict_types=1);

namespace Thinktomorrow\Vine;

use Thinktomorrow\Vine\Commands\Copy;
use Thinktomorrow\Vine\Commands\Move;
use Thinktomorrow\Vine\Queries\Ancestors;
use Thinktomorrow\Vine\Queries\Count;
use Thinktomorrow\Vine\Queries\Pluck;

class DefaultNode implements Node
{
    protected ?Node $parentNode = null;
    protected NodeCollection $children;
    protected $entry;

    public function __construct($entry)
    {
        $this->replaceNodeEntry($entry);

        $this->children = new NodeCollection();
    }

    public function getNodeId(): string
    {
        return (string) $this->getNodeEntry('id');
    }

    public function getParentNodeId(): ?string
    {
        return (string) $this->getNodeEntry('parent_id');
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
        return !$this->children->isEmpty();
    }

    public function sortChildNodes($key): Node
    {
        if ($this->hasChildNodes()) {
            $this->children = $this->children->sort($key);
        }

        return $this;
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
        return !!$this->parentNode;
    }

    public function getNodeEntry($key = null, $default = null)
    {
        if (!($key === null)) {

            if(is_array($this->entry)) {
                return isset($this->entry[$key]) ? $this->entry[$key] : $default;
            }

            return isset($this->entry->{$key}) ? $this->entry->{$key} : $default;
        }

        return $this->entry;
    }

    public function replaceNodeEntry($entry): void
    {
        $this->entry = $entry;
    }

    public function removeNode(Node $node): Node
    {
        $this->getChildNodes()->remove($node);
        $node->parentNode = null; // Removes parent relation

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
        if (!$this->isRootNode()) {
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
        return !$this->hasParentNode();
    }

    public function hasNodeEntryValue($key, $value): bool
    {
        return in_array($this->getNodeEntry($key), (array) $value);
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
     * @param bool            $down
     *
     * @return array
     */
    public function pluckChildNodes($key, $value = null, $down = true): array
    {
        return $this->pluck($key, $value);
    }

    /**
     * Get flat array of plucked values from child nodes.
     *
     * @param $key
     * @param null $value
     *
     * @return array
     */
    public function pluckAncestorNodes($key, $value = null): array
    {
        return $this->pluck($key, $value, false);
    }

    private function pluck($key, $value = null, $down = true): array
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
        $copy->children = new NodeCollection();
        $copy->parentNode = null;

        return $copy;
    }

    /**
     * Reduce collection to the nodes that pass the callback
     * Shaking a collection will keep the ancestor structure.
     *
     * @param callable $callback
     *
     * @return self
     */
    public function shakeChildNodes(callable $callback): Node
    {
        $node = $this->copyIsolatedNode();

        return $this->isLeafNode() ? $node : $node->addChildNodes($this->getChildNodes()->shake($callback));
    }

    /**
     * Same as shaking except that it will not keep the ancestor structure.
     *
     * @param callable $callback
     *
     * @return self
     */
    public function pruneChildNodes(callable $callback): Node
    {
        $node = $this->copyIsolatedNode();

        return $this->isLeafNode() ? $node : $node->addChildNodes($this->getChildNodes()->prune($callback));
    }

    /**
     * @param $children
     *
     * @return NodeCollection
     */
    private function transformToNodeCollection($children): NodeCollection
    {
        if (is_array($children)) {
            $children = new NodeCollection(...$children);
        } elseif ($children instanceof self) {
            $children = new NodeCollection($children);
        } elseif (!$children instanceof NodeCollection) {
            throw new \InvalidArgumentException('Invalid children parameter. Accepted types are array or NodeCollection.');
        }

        return $children;
    }
}
