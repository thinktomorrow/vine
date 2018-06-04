<?php

namespace Tests\Implementations\Transposers;

use Vine\Transposers\Transposable;

/**
 * expected input:
 * { id: 1, parent_id: 0, label: 'foobar', ... }
 */
class ArrayTransposer implements Transposable
{
    /**
     * @var array
     */
    private $flatten;

    public function __construct(array $flatten)
    {
        $this->flatten = $flatten;
    }

    public function all(): array
    {
        return $this->flatten;
    }

    public function key(): string
    {
        return 'id';
    }

    public function parentKey(): string
    {
        return 'parent_id';
    }
}