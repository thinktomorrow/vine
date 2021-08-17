<?php

namespace Vine\Sources;

use Vine\Node;
use Vine\Source;

class ArraySource implements Source
{
    /** @var array */
    private $flatten;

    public function __construct(array $flatten)
    {
        $this->flatten = $flatten;
    }

    public function nodeEntries(): array
    {
        return $this->flatten;
    }

    public function nodeKeyIdentifier(): string
    {
        return 'id';
    }

    public function nodeParentKeyIdentifier(): string
    {
        return 'parent_id';
    }

    public function createNode($entry): Node
    {
        return ($entry instanceof Node) ? $entry : new Node($entry);
    }
}
