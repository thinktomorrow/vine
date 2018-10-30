<?php

use PHPUnit\Framework\TestCase;
use Vine\Node;
use Vine\NodeCollection;

class SortNodeCollectionTest extends TestCase
{
    /** @test */
    public function it_can_sort_collection_of_array_entries()
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

    /** @test */
    public function it_can_sort_collection_of_object_entries()
    {
        $collection = new NodeCollection(
            new Node((object) ['id' => 2]),
            new Node((object) ['id' => 4]),
            new Node((object) ['id' => 1])
        );

        $this->assertEquals(new NodeCollection(
            new Node((object) ['id' => 1]),
            new Node((object) ['id' => 2]),
            new Node((object) ['id' => 4])
        ), $collection->sort('id'));
    }

    /** @test */
    public function it_can_call_sort_on_a_node()
    {
        $original = new Node((object) ['id' => 2]);
        $original->addChildren([new Node(['id' => '23']), new Node(['id' => '22']), new Node(['id' => '21'])]);

        $expected = new Node((object) ['id' => 2]);
        $expected->addChildren([new Node(['id' => '21']), new Node(['id' => '22']), new Node(['id' => '23'])]);

        $this->assertEquals($expected, $original->sort('id'));
    }

    /** @test */
    public function it_can_sort_nested_collection()
    {
        $collection = new NodeCollection(
            $parent = new Node((object) ['id' => 2]),
            new Node((object) ['id' => 4]),
            new Node((object) ['id' => 1])
        );

        $parent->addChildren([new Node(['id' => '23']), new Node(['id' => '22']), new Node(['id' => '21'])]);

        $parent2 = new Node((object) ['id' => 2]);
        $parent2->addChildren([new Node(['id' => '21']), new Node(['id' => '22']), new Node(['id' => '23'])]);

        $this->assertEquals(new NodeCollection(
            new Node((object) ['id' => 1]),
            $parent2,
            new Node((object) ['id' => 4])
        ), $collection->sort('id'));
    }

    /** @test */
    public function if_sorting_key_does_not_exist_unsorted_entries_are_expected()
    {
        $original = new Node((object) ['id' => 2]);
        $original->addChildren([new Node(['id' => '23']), new Node(['id' => '22']), new Node(['id' => '21'])]);

        $expected = new Node((object) ['id' => 2]);
        $expected->addChildren([new Node(['id' => '23']), new Node(['id' => '22']), new Node(['id' => '21'])]);

        $this->assertEquals($expected, $original->sort('unknown'));
    }
}
