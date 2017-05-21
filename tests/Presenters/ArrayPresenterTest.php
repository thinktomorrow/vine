<?php

use PHPUnit\Framework\TestCase;
use Tests\Implementations\Presenters\ArrayPresenter;
use Tests\Fixtures\FixtureTransposer;
use Vine\Node;
use Vine\Transposable;

class ArrayPresenterTest extends TestCase
{
    /** @test */
    function it_can_represent_a_collection_as_array()
    {

        $result = (new ArrayPresenter)->collection(new \Vine\NodeCollection(new Node(null)))->render();

        $this->assertInternalType('array',$result);
    }

    /** @test */
    function it_can_use_tree_as_source()
    {
        $tree = (new \Vine\TreeFactory)->create($this->getTranslation());

        $result = (new ArrayPresenter)->collection($tree)->render();

        $this->assertInternalType('array',$result);
    }

    /**
     * @return Transposable
     */
    private function getTranslation(): Transposable
    {
        return new FixtureTransposer('default');
    }
}