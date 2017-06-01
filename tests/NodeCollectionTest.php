<?php

use PHPUnit\Framework\TestCase;
use Vine\Node;
use Vine\NodeCollection;

class NodeCollectionTest extends TestCase
{
    /** @test */
    function it_contains_array_of_nodes()
    {
        $collection = new NodeCollection();

        $this->assertInternalType('array', $collection->all());
        $this->assertCount(0, $collection->all());
    }

    /** @test */
    function it_accepts_variadic_array_of_nodes()
    {
        $collection = new NodeCollection(
            new Node('foobar'),
            new Node('foobar-2')
        );

        $this->assertCount(2, $collection->all());
    }

    /** @test */
    function it_can_add_array_of_nodes()
    {
        $collection = new NodeCollection(
            new Node('foobar')
        );

        $collection->add(
            new Node('foobar-2'),
            new Node('foobar-3')
        );

        $this->assertCount(3, $collection->all());
    }

    /** @test */
    function it_can_get_total_count_of_all_nodes_and_children()
    {
        $collection = new NodeCollection(
            (new Node(['id' => 1]))
                ->addChildren((new Node(['id' => 2]))
                    ->addChildren(new Node(['id' => 3]))
                ),
            new Node(['id' => 4])
        );

        $this->assertEquals(4,$collection->total());
        $this->assertEquals(2,$collection->first()->children()->total());
    }

    /** @test */
    function it_can_sort_collection()
    {
        $collection = new NodeCollection(
            new Node(['id' => 2]),
            new Node(['id' => 4]),
            new Node(['id' => 1])
        );

        $this->assertEquals(new NodeCollection(
            new Node(['id' => 1]),
            new Node(['id' => 2]),
            new Node(['id' => 4])
        ), $collection->sort('id'));
    }
}