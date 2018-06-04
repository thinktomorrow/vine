<?php

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\FixtureSource;
use Tests\Implementations\Presenters\CliPresenter;
use Vine\Source;

class CliPresenterTest extends TestCase
{
    /** @test */
    function it_can_represent_tree_in_terminal()
    {
        $tree = (new \Vine\NodeCollectionFactory)->create($this->getTranslation());

        $output = (new CliPresenter)->collection($tree)->render();

        $this->assertInternalType('string',$output);
        $this->assertStringStartsWith('|-root-1',trim($output,PHP_EOL));
    }

    /**
     * @return Source
     */
    private function getTranslation(): Source
    {
        return new FixtureSource('default');
    }
}