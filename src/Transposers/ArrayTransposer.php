<?php

namespace Vine\Transposers;

class ArrayTransposer implements Transposable
{
    /** @var array */
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