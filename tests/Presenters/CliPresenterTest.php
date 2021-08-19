<?php

namespace Thinktomorrow\Vine\Tests\Presenters;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\NodeCollectionFactory;
use Thinktomorrow\Vine\Tests\Fixtures\FixtureSource;
use Thinktomorrow\Vine\Presenters\CliPresenter;
use Thinktomorrow\Vine\Source;

class CliPresenterTest extends TestCase
{
    /** @test */
    public function it_can_represent_tree_in_terminal()
    {
        $tree = (new NodeCollectionFactory())->fromSource($this->getTranslation());

        $output = (new CliPresenter())->collection($tree)->render();

        $this->assertIsString($output);
        $this->assertStringStartsWith('|-root-1', trim($output, PHP_EOL));
    }

    /**
     * @return Source
     */
    private function getTranslation(): Source
    {
        return new FixtureSource('default');
    }
}
