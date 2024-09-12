# Vine Package Upgrade Guide from 0.4 to 0.5

## Key Changes Overview

The new version of the Vine package includes **performance improvements** and **API adjustments**. Below is a summary of the changes, with instructions on how to update your implementation.

### 1. **Performance Enhancements**
The core logic of node operations has been optimized, particularly around:
- **Node creation**: Reduces overhead when creating large collections.
- **Recursive methods**: Optimized to minimize memory usage.

No changes are needed in your code for these optimizations.

### 2. **API Changes**

#### 2.1. `fromArray()` Changes
In the new version, `fromArray()` now accepts an additional `$parentKey` argument, allowing better control over parent-child relationships in large data sets.

**Old usage:**
```php
$collection = NodeCollection::fromArray($nodes);
```

**New usage:**
```php
$collection = NodeCollection::fromArray($nodes, 'parent_id');
```

#### 2.2. `pluck()` Method Behavior
The `pluck()` method has been refactored for performance. It no longer merges duplicate keys by default. If you need the old behavior, pass a new optional parameter `$mergeKeys`.

**Old usage:**
```php
$plucked = $collection->pluck('id');
```

**New usage:**
```php
$plucked = $collection->pluck('id', null, true);  // Enables key merging
```

#### 2.3. `mapRecursive()` Improvement
The `mapRecursive()` method now supports a third optional `$depth` parameter, allowing control over how deep the recursion should go.

**Old usage:**
```php
$collection->mapRecursive(function($node) { ... });
```

**New usage:**
```php
$collection->mapRecursive(function($node) { ... }, 3);  // Limits recursion to 3 levels
```

### 3. Deprecated Methods

- **`shake()`**: Replaced by more performant `prune()` with identical functionality.
    - **Migration**: Replace all instances of `shake()` with `prune()`.

**Old usage:**
```php
$collection->shake(function($node) { ... });
```

**New usage:**
```php
$collection->prune(function($node) { ... });
```

### 4. **NodeCollectionFactory Refactor**
The `NodeCollectionFactory` class has undergone significant refactoring for better performance. Ensure that any custom factories extending this class follow the new internal structure.

### Final Notes
Make sure to test the updated package in your development environment, especially if you are working with large datasets, to benefit from the performance improvements.
