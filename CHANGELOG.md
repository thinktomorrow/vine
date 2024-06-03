# Changelog
All Notable changes to the `vine` package will be documented in this file. Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/)
principles.

## unreleased
**Warning: this release contains breaking changes. Please read the following upgrade guide before updating.**
- Removed: `NodeCollection::fromSource` method. Use `NodeCollection::fromArray` or `NodeCollection::fromIterable` instead.
- Removed: `Source` interface and `NodeSource` in line with Source removal. 
- Added: option to use model as a Node. Before a model was always an entry property on the Node object. Now you can use your models as Nodes. Make sure they implement the Node interface.
- Changed: DefaultNode has an extra parameter. Second argument to the constructor should be the (empty) nodeCollection for the children collection. This ensures that a custom NodeCollection is used throughout the tree.
- Added: `NodeCollection::fromIterable()` method. This allows to easily use the NodeCollection in different places in your project. Also the creation of a Node is done via a callable as optional second parameter. This way you can customize the creation of the Node.
```
public static function filterTree(): NodeCollection {
return NodeCollection::fromArray(self::all()->toArray());

    return NodeCollection::fromIterable(self::where('active', true)->get());
    
    // or
    
    RETURN NodeCollection::fromSource(new ArraySource(self::all()->toArray()));
}
```
- Changed: It is no longer advised to directly use the `NodeCollectionFactory` class for composing a tree. This is an internals class. Instead, use the `NodeCollection::fromArray` or `NodeCollection::fromIterable` methods.
- e.g. 

## TODO:
- nodeDefaults trait to use model as node itself
- trait for nodeEntry stuff on when to use entry as property in defaultNode instead of node itself
- test: childcollection should be same as custom nodecollection
- resolve all todo's in files
  - factory: structure collection can be replaced by just calls in the own method
  - toArray() tests
  - better debugging experience with the array / cli helper: visualize the tree. 
  - documentation.

## 0.4.10 - 2023-02-03
- Fixed: when parent id was null, an empty string was returned by getParentNodeId() instead of expected null.

## 0.4.9 - 2022-11-18
- Added: Node::getSiblingNodes() and Node::hasSiblingNodes(). This returns the nodes that share the parent of current node. Please note that you cannot collect root nodes via this method.
- Added: Node::getLeftSiblingNode() and Node::getRightSiblingNode(). This will return the node left or right to the current one. Ideal for returning previous / next models.
- Fixed: Honour custom node collection class when transforming to an empty nodeCollection.

## 0.4.8 - 2022-10-26
- Added: allow a Closure on the nodeCollection::pluck method for both key and value. e.g. $collection->pluck(fn($node) => $node->getNodeId())
