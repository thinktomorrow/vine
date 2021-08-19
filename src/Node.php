<?php

namespace Thinktomorrow\Vine;

interface Node
{
    public function getNodeId(): string;

    public function getParentNodeId(): ?string;

    public function equalsNode(Node $other): bool;

    /**
     * At which depth does this node resides inside the entire tree.
     *
     * @return int
     */
    public function getNodeDepth(): int;

    /**
     * count of all direct child nodes.
     *
     * @return int
     */
    public function getChildNodesCount(): int;

    /**
     * Total of all child nodes.
     *
     * @return int
     */
    public function getTotalChildNodesCount(): int;

    public function isLeafNode(): bool;

    public function isRootNode(): bool;

    /**
     * @param array|NodeCollection|Node $children
     *
     * @return Node
     */
    public function addChildNodes($children): Node;

    public function getChildNodes(): NodeCollection;

    public function hasChildNodes(): bool;

    public function sortChildNodes($key): Node;

    public function findChildNodes($key, array $values): NodeCollection;

    public function findChildNode($key, $value): Node;

    public function getParentNode(): ?Node;

    public function setParentNode(Node $parent): Node;

    public function hasParentNode(): bool;

    public function getNodeEntry($key = null, $default = null);

    public function replaceNodeEntry($entry): void;

    public function hasNodeEntryValue($key, $value): bool;

    /**
     * Removes a child node.
     * It deletes that child node from any depth in the graph.
     * Also removes the entire children tree from that removed node!
     *
     * @param Node $node
     *
     * @return $this
     */
    public function removeNode(Node $node): Node;

    /**
     * Move a child node to a different parent.
     *
     * @param Node $parent
     *
     * @return mixed
     */
    public function moveToParentNode(Node $parent);

    public function moveNodeToRoot();

    /**
     * @param int|null $depth
     *
     * @return NodeCollection
     */
    public function getAncestorNodes($depth = null): NodeCollection;

    /**
     * Get flat array of plucked values from child nodes.
     *
     * @param string|int $key
     * @param string|int|null $value
     * @param bool $down
     *
     * @return array
     */
    public function pluckChildNodes($key, $value = null, $down = true): array;

    /**
     * Get flat array of plucked values from child nodes.
     *
     * @param $key
     * @param null $value
     *
     * @return array
     */
    public function pluckAncestorNodes($key, $value = null): array;

    /**
     * Get a copy of this node.
     *
     * @param int|null $depth
     *
     * @return Node
     */
    public function copyNode($depth = null): Node;

    /**
     * Copy of this node without its parent / children relationships.
     *
     * @return Node
     */
    public function copyIsolatedNode(): Node;

    /**
     * Reduce collection to the nodes that pass the callback
     * Shaking a collection will keep the ancestor structure.
     *
     * @param callable $callback
     *
     * @return Node
     */
    public function shakeChildNodes(callable $callback): Node;

    /**
     * Same as shaking except that it will not keep the ancestor structure.
     *
     * @param callable $callback
     *
     * @return Node
     */
    public function pruneChildNodes(callable $callback): Node;
}
