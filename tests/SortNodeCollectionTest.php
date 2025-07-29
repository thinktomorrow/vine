<?php

namespace Thinktomorrow\Vine\Tests;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\DefaultNode;
use Thinktomorrow\Vine\NodeCollection;

class SortNodeCollectionTest extends TestCase
{
    public function test_it_can_sort_collection_of_array_entries()
    {
        $collection = new NodeCollection([
            new DefaultNode(['id' => 2]),
            new DefaultNode(['id' => 4]),
            new DefaultNode(['id' => 1]),
        ]);

        $this->assertEquals(new NodeCollection([
            new DefaultNode(['id' => 1]),
            new DefaultNode(['id' => 2]),
            new DefaultNode(['id' => 4]),
        ]), $collection->sort('id'));
    }

    public function test_it_can_sort_collection_of_object_entries()
    {
        $collection = new NodeCollection([
            new DefaultNode((object) ['id' => 2]),
            new DefaultNode((object) ['id' => 4]),
            new DefaultNode((object) ['id' => 1]),
        ]);

        $this->assertEquals(new NodeCollection([
            new DefaultNode((object) ['id' => 1]),
            new DefaultNode((object) ['id' => 2]),
            new DefaultNode((object) ['id' => 4]),
        ]), $collection->sort('id'));
    }

    public function test_it_can_call_sort_on_a_node()
    {
        $original = new DefaultNode((object) ['id' => 2]);
        $original->addChildNodes([new DefaultNode(['id' => '23']), new DefaultNode(['id' => '22']), new DefaultNode(['id' => '21'])]);

        $expected = new DefaultNode((object) ['id' => 2]);
        $expected->addChildNodes([new DefaultNode(['id' => '21']), new DefaultNode(['id' => '22']), new DefaultNode(['id' => '23'])]);

        $this->assertEquals($expected, $original->sortChildNodes('id'));
    }

    public function test_it_can_sort_nested_collection()
    {
        $collection = new NodeCollection([
            $parent = new DefaultNode((object) ['id' => 2]),
            new DefaultNode((object) ['id' => 4]),
            new DefaultNode((object) ['id' => 1]),
        ]);

        $parent->addChildNodes([new DefaultNode(['id' => '23']), new DefaultNode(['id' => '22']), new DefaultNode(['id' => '21'])]);

        $parent2 = new DefaultNode((object) ['id' => 2]);
        $parent2->addChildNodes([new DefaultNode(['id' => '21']), new DefaultNode(['id' => '22']), new DefaultNode(['id' => '23'])]);

        $this->assertEquals(new NodeCollection([
            new DefaultNode((object) ['id' => 1]),
            $parent2,
            new DefaultNode((object) ['id' => 4]),
        ]), $collection->sort('id'));
    }

    public function test_if_sorting_key_does_not_exist_unsorted_entries_are_expected()
    {
        $original = new DefaultNode((object) ['id' => 2]);
        $original->addChildNodes([new DefaultNode(['id' => '23']), new DefaultNode(['id' => '22']), new DefaultNode(['id' => '21'])]);

        $expected = new DefaultNode((object) ['id' => 2]);
        $expected->addChildNodes([new DefaultNode(['id' => '23']), new DefaultNode(['id' => '22']), new DefaultNode(['id' => '21'])]);

        $this->assertEquals($expected, $original->sortChildNodes('unknown'));
    }
}
