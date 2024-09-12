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

    /**
     * Get Root node of this node tree.
     * In case this node is a root, this node will be returned as well
     */
    public function getRootNode(): Node;

    public function getParentNode(): ?Node;

    public function setParentNode(Node $parent): Node;

    public function removeParentNode(): Node;

    public function hasParentNode(): bool;

    public function getSiblingNodes(): NodeCollection;
    public function hasSiblingNodes(): bool;
    public function getLeftSiblingNode(): ?Node;
    public function getRightSiblingNode(): ?Node;

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
     * @param bool $includeSelf
     *
     * @return array
     */
    public function pluckChildNodes($key, $value = null, bool $includeSelf = false): array;

    /**
     * Get flat array of plucked values from child nodes.
     *
     * @param string $key
     * @param null $value
     *
     * @return array
     */
    public function pluckAncestorNodes(string $key, $value = null, bool $includeSelf = false): array;

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

    /**
     * Does the node has this given value. Used by the
     * find logic to filter nodes based on their value.
     */
    public function hasNodeValue($key, $value): bool;

    public function getNodeValue($key, $default = null): mixed;

    /**
     * Used by the Collection sort method to retrieve the value to sort on.
     *
     * @param $key
     * @return mixed
     */
    public function getSortValue($key);
}
