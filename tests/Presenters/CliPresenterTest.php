<?php

namespace Thinktomorrow\Vine\Tests\Presenters;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\NodeCollection;
use Thinktomorrow\Vine\NodeCollectionFactory;
use Thinktomorrow\Vine\Presenters\CliPresenter;
use Thinktomorrow\Vine\Source;
use Thinktomorrow\Vine\Tests\Fixtures\FixtureSource;

class CliPresenterTest extends TestCase
{
    /** @test */
    public function it_can_represent_tree_in_terminal()
    {
        $tree = NodeCollection::fromIterable($this->getTranslation());

        $output = (new CliPresenter())->collection($tree)->render();
die(var_dump($output));
        $this->assertIsString($output);
        $this->assertStringStartsWith('|-root-1', trim($output, PHP_EOL));
    }

    private function getTranslation(): iterable
    {
        return (new FixtureSource('default'))->get();
    }
}
