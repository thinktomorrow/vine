
# Vine
`Thinktomorrow\Vine` is a PHP package designed to manage and manipulate **adjacent tree-structured models**. It provides a powerful interface for building and querying collections of hierarchical data, with methods to traverse, sort, and manipulate node collections in a flexible way.

It's key features are:
- **Tree Traversal**: Map and traverse nodes with recursion.
- **Manipulation**: Add, remove, or merge nodes.
- **Flattening & Inflating**: Convert hierarchical data to flat lists and vice versa.
- **Customizable Queries**: Find nodes based on specific attributes.

**IMPORTANT**
An adjacent tree structure is assumed, where each node has an `id` and `parent_id` attribute.

## Installation
Install the package via composer:

```bash
composer require thinktomorrow/vine
```

## Basic Usage

### 1. Creating a Node Collection
You can create a node collection from an array of nodes. 

```php
use Thinktomorrow\Vine\NodeCollection;
use Thinktomorrow\Vine\Node;

// Assuming Node is a model implementing the Node interface
$nodes = [
    new Node(['id' => 1, 'name' => 'Parent']),
    new Node(['id' => 2, 'name' => 'Child', 'parent_id' => 1])
];

$collection = NodeCollection::fromArray($nodes);
```

### 2. Traversing Nodes
The package allows you to iterate over nodes recursively.

```php
$collection->eachRecursive(function($node) {
    echo $node->getName();  // Access node attributes
});
```

### 3. Flattening a Tree
To get a flat array of all nodes:

```php
$flatNodes = $collection->flatten()->toArray();
```

### 4. Inflating a Flattened Collection
You can restore a flattened collection back to its tree structure:

```php
$inflatedCollection = $collection->inflate();
```

### 5. Finding Nodes
To find nodes by specific attributes:

```php
$node = $collection->find('id', 1); // Find a node with id = 1
```

Or find multiple nodes by a set of values:

```php
$nodes = $collection->findMany('id', [1, 2, 3]); // Find nodes with ids 1, 2, and 3
```

### 6. Adding and Merging Nodes
You can add or merge nodes into the collection:

```php
$newNode = new Node(['id' => 3, 'name' => 'New Child']);
$collection->add($newNode); // Add a new node

$otherCollection = NodeCollection::fromArray([...]); // Another node collection
$collection->merge($otherCollection); // Merge collections
```

### 7. Sorting Nodes
Nodes can be sorted by any attribute:

```php
$sortedCollection = $collection->sort('name'); // Sort by 'name' attribute
```

### 8. Removing Nodes
To remove nodes by attribute or condition:

```php
$collection = $collection->remove(function($node) {
    return $node->getId() == 2; // Remove node with id 2
});
```

## Example: Full Usage

```php
use Thinktomorrow\Vine\NodeCollection;
use Thinktomorrow\Vine\Node;

$nodes = [
    new Node(['id' => 1, 'name' => 'Root']),
    new Node(['id' => 2, 'name' => 'Child 1', 'parent_id' => 1]),
    new Node(['id' => 3, 'name' => 'Child 2', 'parent_id' => 1])
];

$collection = NodeCollection::fromArray($nodes);

// Add a node
$collection->add(new Node(['id' => 4, 'name' => 'New Child', 'parent_id' => 2]));

// Flatten, sort, and remove
$flatNodes = $collection->flatten()->sort('name')->remove(function($node) {
    return $node->getName() === 'Child 2';
});

// Output as array
print_r($flatNodes->toArray());
```
