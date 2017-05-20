<?php

use PHPUnit\Framework\TestCase;
use Tests\Implementations\Presenters\ArrayPresenter;
use Tests\Fixtures\FixtureDataTransposer;
use Vine\DataTransposer;

class ArrayPresenterTest extends TestCase
{
    /** @test */
    function it_can_represent_tree_as_array()
    {
        $tree = (new \Vine\TreeFactory)->create($this->getTranslation());

        $result = (new ArrayPresenter)->tree($tree)->render();

        $this->assertInternalType('array',$result);
    }

    /**
     * @return DataTransposer
     */
    private function getTranslation(): DataTransposer
    {
        return new FixtureDataTransposer('default');
    }
}