# Vine

Render an adjacent datamodel to the desired html output.

[![Build Status](https://travis-ci.org/thinktomorrow/vine.svg?branch=master)](https://travis-ci.org/thinktomorrow/vine)
[![Coverage Status](https://coveralls.io/repos/github/thinktomorrow/vine/badge.svg?branch=master)](https://coveralls.io/github/thinktomorrow/vine?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thinktomorrow/vine/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/thinktomorrow/vine/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/573b8ce5-0c73-432c-9ddb-57a1c16bff8d/mini.png)](https://insight.sensiolabs.com/projects/573b8ce5-0c73-432c-9ddb-57a1c16bff8d)

## Example

```php
// Load flat dataset from storage
$dataset = [
    ['id' => 1, 'parent_id' => 0, 'label' => 'foobar'],
    ['id' => 2, 'parent_id' => 1, 'label' => 'baz'],
    ['id' => 3, 'parent_id' => 2, 'label' => 'bazbaz'],
    ...
];

// Translate this dataset to a Vine tree object
$tree = (new \Vine\TreeFactory)->create(
    new ExampleTranslator($dataset)
);

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