<?php

namespace Thinktomorrow\Vine\Tests\Fixtures;

use Thinktomorrow\Vine\DefaultNode;
use Thinktomorrow\Vine\Node;
use Thinktomorrow\Vine\NodeCollection;

class FixtureSource
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

    public function get(): array
    {
        return $this->flatten;
    }

    public function getAsCollection(): NodeCollection
    {
        return NodeCollection::fromIterable($this->flatten, function ($entry) {
            return new DefaultNode($entry, new NodeCollection(), '0', '1');
        });
    }
}
