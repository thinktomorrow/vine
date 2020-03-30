
[![Build Status](https://travis-ci.org/thinktomorrow/vine.svg?branch=master)](https://travis-ci.org/thinktomorrow/vine)
[![Coverage Status](https://coveralls.io/repos/github/thinktomorrow/vine/badge.svg?branch=master)](https://coveralls.io/github/thinktomorrow/vine?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thinktomorrow/vine/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/thinktomorrow/vine/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/573b8ce5-0c73-432c-9ddb-57a1c16bff8d/mini.png)](https://insight.sensiolabs.com/projects/573b8ce5-0c73-432c-9ddb-57a1c16bff8d)

# Vine

Render an adjacent datamodel to the desired html output.

## Create a single node
```php
// Create a node and attach a child node ot it
$node = new Node('foobar');
$node->addChildren(new Node('fooberry'));
```
This is fine when dealing with small, isolated datasets. When you need to inject a larger amount of records, you'll want 
to load all that data at once. 

## Using a dataset
Usually you'll want to use a collection with values coming from a database. Two methods provide this functionality:
`NodeCollection::fromArray()` and `NodeCollection::fromSource()` allow you to transpose an entire array of records at once to a node collection.

When using `NodeCollection::fromArray()`, it is assumed that each record has the following:
 - a property `id` which provides an unique reference to the entry.
 - a property `parent_id` which is used by a child entry to refer to its parent entry.

```php
// flat dataset as pulled from database
$dataset = [
    ['id' => 1, 'parent_id' => 0, 'label' => 'foobar'],
    ['id' => 2, 'parent_id' => 1, 'label' => 'baz'],
    ['id' => 3, 'parent_id' => 2, 'label' => 'bazbaz'],
];

$collection = NodeCollection::fromArray($dataset);
```

## Using a custom dataset
 You can provide a custom data source. Here's how you use it:
 ```php
 // Using a custom source
 $collection = NodeCollection::fromSource(
     new ArraySource($dataset)
 );
 ```
 
 This is useful when your data does not contain the default`id` or `parent_id` properties. 
 With a custom source, you can set which is the `nodeKeyIdentifier` (id) and the `nodeParentKeyIdentifier` (parent id). A custom source should honour the `\Vine\Source` interface:
 ```php 
 interface Source
 {
    // array of all entries.
     public function nodeEntries(): array;

     // property to identify the key (default is 'id')
     public function nodeKeyIdentifier(): string;

     // property to identify the parent key (default is 'parent_id')
     public function nodeParentKeyIdentifier(): string;
 }
 ```
 
## NodeCollection Api
- all(): array - Return all the nodes as array
- first(): ?Node - Return the first node
- last(): ?Node - Return the last node
- isEmpty(): bool - checks if this collection is empty.
- findMany($key, array $values): NodeCollection - Find nodes by value.
- find($key, $value): ?Node - Find a node by value.
- total(): int - total of all nodes, including their children.
- count(): int - total of all top level nodes.
- add(Node ...$nodes) - add a new Node to the collection
- merge(NodeCollection $nodeCollection) - merge another collection into this one.
- map(callable $callback) - loop over and modify each top level node value 
- mapRecursive(callable $callback) - loop over and modify each node value 
- each(callable $callback) - loop over each top level node value 
- sort($key) - sort all the nodes by given key
- copy($depth = null): NodeCollection - copy the collection into a new NodeCollection
- remove(Node $child) - remove a given Node from this collection.
- flatten(): NodeCollection - Return flattened list of all nodes in this collection.
- inflate(): NodeCollection - Inflate a flattened collection back to its original structure.
- pluck($key, $value = null, $down = true): array - Get flat array of plucked values from child nodes.
- slice(Node ...$nodes): NodeCollection - Slice one or more nodes out of the collection.
- shake(callable $callback): NodeCollection - Filter collection to those nodes that pass the callback (Shaking a collection will keep the ancestor structure).   
- prune(callable $callback): NodeCollection - Same as shake() except that it will not keep the ancestor structure.   

## Node Api
- equals(Node $other) - check if the given node is the same object as this one
- addChildren($children) - add Nodes as children of this node.
- children(): NodeCollection - get the direct children of this node.
- hasChildren(): bool - check if this node has any children. 
- sort($key) - sort the children by given sort key. 
- entry($key = null, $default = null) - Return the data entry or a given property of the entry. 
- replaceEntry($entry) - Replace the entry data with the given parameter. 
- parent(Node $parent = null) - Return the parent or, if argument is passed, set the parent for this Node. 
- remove(Node $node = null) - Remove this node or detaches a child node.
- move(Node $parent) - Move a child node to a different parent.
- moveToRoot() - Move the node to the top level.
- depth(): int - At which depth does this node resides inside the entire tree.
- count(): int - Return the count of all direct child nodes.
- total(): int - Return the total count of all child nodes.    
- isLeaf(): bool - Is this Node a leaf node? Meaning it has no child nodes.
- isRoot(): bool - Is this Node a root node? Meaning it has no parent. 
- has($key, $value): bool - Does the Node has given value in its data entry?
- findMany($key, array $values): NodeCollection - Find child nodes by given value. 
- find($key, $value): ?Node - Find child node by given value. 
- ancestors($depth = null): NodeCollection - Return all ancestor nodes of this node. 
- pluck($key, $value = null, $down = true): array - Get flat array of plucked values from child nodes.
- pluckAncestors($key, $value = null, $down = true): array - Get flat array of plucked values from parent nodes.
- copy($depth = null): Node - Creates and returns a copy of this node. 
- isolatedCopy(): Node - Creates and returns a copy of this node without parent or child relationships. 
- shake(callable $callback): Node - Filter collection to the nodes that pass the callback (Shaking a collection will keep the ancestor structure).
- prune(callable $callback): Node - Same as shake() except that it will not keep the ancestor structure.
