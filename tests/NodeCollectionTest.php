<?php

use PHPUnit\Framework\TestCase;
use Vine\Node;
use Vine\NodeCollection;
use Vine\Sources\ArraySource;

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
    function it_accepts_an_array_of_nodes()
    {
        $collection = NodeCollection::fromArray([
            new Node('foobar'),
            new Node('foobar-2')
        ]);

        $this->assertCount(2, $collection->all());
    }

    /** @test */
    function it_accepts_a_transposer()
    {
        $collection = NodeCollection::fromSource(new ArraySource([
            new Node('foobar'),
            new Node('foobar-2')
        ]));

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
    function it_can_change_each_child_node_with_a_callback()
    {
        $original = new Node((object)['id' => 2]);
        $original->addChildren([new Node(['id' => '23']), new Node(['id' => '22']), new Node(['id' => '21'])]);

        $original->children()->map(function($node){
            $entry = $node->entry();
            $entry['title'] = 'new';
            return $node->replaceEntry($entry);
        });

        $expected = new Node((object)['id' => 2]);
        $expected->addChildren([new Node(['id' => '23', 'title' => 'new']), new Node(['id' => '22', 'title' => 'new']), new Node(['id' => '21', 'title' => 'new'])]);

        $this->assertEquals($expected, $original);
    }
}