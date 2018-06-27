
# Vine

Render an adjacent datamodel to the desired html output.

## Quick start example
```php
// Create a node
$node = new Node('foobar');

// Attach a child node
$node->addChildren(new Node('fooberry'));
```

```php
// Check if node has no children
$node->isLeaf(); // returns false or true

// Check depth of node in tree, starts from zero.
$child->depth(); // returns 1
```

## Loading a dataset
Usually you'll want to use a collection with values coming from a database. This data is fetched as a flat list of records.
 With the transposer pattern you are able to convert this structure to a nested node collection.

```php
// flat dataset as pulled from database
$dataset = [
    ['id' => 1, 'parent_id' => 0, 'label' => 'foobar'],
    ['id' => 2, 'parent_id' => 1, 'label' => 'baz'],
    ['id' => 3, 'parent_id' => 2, 'label' => 'bazbaz'],
];

// Convert this dataset to a node collection
$collection = NodeCollection::fromArray($dataset);
```

This will return a NodeCollection which includes the hierarchy of this dataset.

## Loading a dataset from a model

If you want to create a hierarchical sturcture based on a model (like Categories for a menu, ...) we need to do the following:
To get a model to use vine we need to implement Vine\Source.

```php
Vine\Source as VineSource;

class Category implements VineSource{

}
```

This will require you to override 3 methods, which are used in determining what the structure will be:

This function is used when we want to have controll over the source data set structure.
The default would return all the results:

```php
public function nodeEntries(){
    return static:all()->toArray();
}
```

In this function we set what the id field is named.
```php
public function nodeKeyIdentifier()
```

In this function we set what the parent_id field is named.
```php
public function nodeParentKeyIdentifier()
```

## Render a collection
You can loop over the nodes as if it was an array, since it implements the Iteratable interface.
```php

$output = (new CliPresenter)->tree($tree)->render();
```

```php
// Render the tree
$output = (new CliPresenter)->tree($tree)->render();
```

This will output something similar to:
```bash
|-foobar
|-Array
| |-baz
| \-Array
|   \-bazbaz
```
