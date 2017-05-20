<?php

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\FixtureDataTransposer;
use Tests\Implementations\Presenters\CliPresenter;
use Vine\DataTransposer;

class CliPresenterTest extends TestCase
{
    /** @test */
    function it_can_represent_tree_in_terminal()
    {
        $tree = (new \Vine\TreeFactory)->create($this->getTranslation());

        $output = (new CliPresenter)->tree($tree)->render();

        $this->assertInternalType('string',$output);
        $this->assertStringStartsWith('|-root-1',trim($output,PHP_EOL));
    }

    /**
     * @return DataTransposer
     */
    private function getTranslation(): DataTransposer
    {
        return new FixtureDataTransposer('default');
    }
}