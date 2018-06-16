<?php

use PHPUnit\Framework\TestCase;
use Vine\Presenters\ArrayPresenter;
use Tests\Fixtures\FixtureSource;
use Vine\Node;
use Vine\Source;

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
        $tree = (new \Vine\NodeCollectionFactory)->fromSource($this->getTranslation());

        $result = (new ArrayPresenter)->collection($tree)->render();

        $this->assertInternalType('array',$result);
    }

    /**
     * @return Source
     */
    private function getTranslation(): Source
    {
        return new FixtureSource('default');
    }
}