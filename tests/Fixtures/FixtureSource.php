<?php

namespace Tests\Fixtures;

use Vine\Source;

/**
 * User: bencavens.
 */
class FixtureSource implements Source
{
    private $flatten;

    public function __construct($type = 'default')
    {
        $filename = 'dataFixture.php';

        if ($type == 'large') {
            $filename = 'largeDataFixture.php';
        }

        $this->flatten = require __DIR__.'/'.$filename;
    }

    public function nodeEntries(): array
    {
        return $this->flatten;
    }

    public function nodeKeyIdentifier(): string
    {
        return 0;
    }

    public function nodeParentKeyIdentifier(): string
    {
        return 1;
    }
}
