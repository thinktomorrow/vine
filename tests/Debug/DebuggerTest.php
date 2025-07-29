<?php

namespace Thinktomorrow\Vine\Tests\Debug;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\Debug\Debugger;
use Thinktomorrow\Vine\NodeCollection;
use Thinktomorrow\Vine\Tests\Fixtures\FixtureSource;

class DebuggerTest extends TestCase
{
    public function test_it_can_debug_collection()
    {
        $tree = $this->getCollection();

        $this->assertInstanceOf(Debugger::class, $tree->debug());
        $this->assertIsString((string)$tree->debug());
    }

    private function getCollection(): NodeCollection
    {
        return (new FixtureSource('default'))->getAsCollection();
    }
}
