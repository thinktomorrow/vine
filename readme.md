
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

## Getting the data
Usually you'll want to use a collection with values coming from a database. This data is fetched as a flat list of records.
 Next step is to convert this structure to a nested node collection. By default we assume that each record has the following:
 - a value `id` which provides an unique reference to the entry.
 - a value `parent_id` which is used by a child entry to refer to its parent entry.

```php
// flat dataset as pulled from database
$dataset = [
    ['id' => 1, 'parent_id' => 0, 'label' => 'foobar'],
    ['id' => 2, 'parent_id' => 1, 'label' => 'baz'],
    ['id' => 3, 'parent_id' => 2, 'label' => 'bazbaz'],
];

$collection = NodeCollection::fromArray($dataset);
```

## Loading a custom dataset
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
