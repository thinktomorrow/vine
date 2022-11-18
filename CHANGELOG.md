# Changelog
All Notable changes to the `vine` package will be documented in this file. Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/)
principles.

## unreleased

## 0.4.9 - 2022-11-18
- Added: Node::getSiblingNodes() and Node::hasSiblingNodes(). This returns the nodes that share the parent of current node. Please note that you cannot collect root nodes via this method.
- Added: Node::getLeftSiblingNode() and Node::getRightSiblingNode(). This will return the node left or right to the current one. Ideal for returning previous / next models.
- Fixed: Honour custom node collection class when transforming to an empty nodeCollection.

## 0.4.8 - 2022-10-26
- Added: allow a Closure on the nodeCollection::pluck method for both key and value. e.g. $collection->pluck(fn($node) => $node->getNodeId())
