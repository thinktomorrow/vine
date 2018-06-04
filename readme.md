
[![Build Status](https://travis-ci.org/thinktomorrow/vine.svg?branch=master)](https://travis-ci.org/thinktomorrow/vine)
[![Coverage Status](https://coveralls.io/repos/github/thinktomorrow/vine/badge.svg?branch=master)](https://coveralls.io/github/thinktomorrow/vine?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thinktomorrow/vine/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/thinktomorrow/vine/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/573b8ce5-0c73-432c-9ddb-57a1c16bff8d/mini.png)](https://insight.sensiolabs.com/projects/573b8ce5-0c73-432c-9ddb-57a1c16bff8d)

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

## Loading a dataset from a model

## Load an advanced dataset

 What can you do with transposers?
 This will transpose your flat adjacent datamodel.
 
 key method
 set the unique identifier of each entry
 
 parentKey method
 set the attribute key that identifies the parent. 
 
 optional entry method
 allows to enrich the entry data with custom data. Handy if you rely on the node info to
 enrich the entry data
 
 optional sortChildrenBy method
 
```php
// flat dataset as pulled from database
$dataset = [
    ['id' => 1, 'parent_id' => 0, 'label' => 'foobar'],
    ['id' => 2, 'parent_id' => 1, 'label' => 'baz'],
    ['id' => 3, 'parent_id' => 2, 'label' => 'bazbaz'],
];

// Convert this dataset to a node collection
$collection = NodeCollection::fromTransposer(
    new ArraySource($dataset)
);
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


## TODO
- allow for primitive value to be passed as entry; making sure that find methods work as expected
- allow for regex patterns to be passed to our find methods