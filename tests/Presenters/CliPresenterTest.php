<?php

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\FixtureTransposer;
use Tests\Implementations\Presenters\CliPresenter;
use Vine\Transposers\Transposable;

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
     * @return Transposable
     */
    private function getTranslation(): Transposable
    {
        return new FixtureTransposer('default');
    }
}