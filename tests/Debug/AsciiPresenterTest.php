<?php

namespace Thinktomorrow\Vine\Tests\Debug;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\NodeCollection;
use Thinktomorrow\Vine\Debug\AsciiPresenter;
use Thinktomorrow\Vine\Tests\Fixtures\FixtureSource;

class AsciiPresenterTest extends TestCase
{
    /** @test */
    public function it_can_represent_tree_as_ascii()
    {
        $tree = $this->getCollection();

        $output = (new AsciiPresenter())->render($tree);

        $this->assertIsString($output);
        $this->assertStringStartsWith('-root-1', trim($output, PHP_EOL));
    }

    private function getCollection(): NodeCollection
    {
        return (new FixtureSource('default'))->getAsCollection();
    }
}
