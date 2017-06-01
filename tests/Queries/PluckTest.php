<?php

use PHPUnit\Framework\TestCase;
use Vine\Node;
use Vine\NodeCollection;

class PluckTest extends TestCase
{
    /** @test */
    function it_can_pluck_specific_values_of_all_children()
    {
        $node = new Node(['id' => 1, 'name' => 'foobar']);
        $node->addChildren([$child = new Node(['id' => 2, 'name' => 'first-child'])]);
        $child->addChildren([$child2 = new Node(['id' => 3, 'name' => 'second-child'])]);

        $this->assertEquals([
            1,2,3
        ],$node->pluck('id'));
    }

    /** @test */
    function it_can_pluck_key_value_pairs_of_all_children()
    {
        $node = new Node(['id' => 'one', 'name' => 'foobar']);
        $node->addChildren([$child = new Node(['id' => 'two', 'name' => 'first-child'])]);
        $child->addChildren([$child2 = new Node(['id' => 'three', 'name' => 'second-child'])]);

        $this->assertEquals([
            'one' => 'foobar',
            'two' => 'first-child',
            'three' => 'second-child',
        ],$node->pluck('id','name'));
    }

    /** @test */
    function it_can_pluck_key_value_pairs_of_all_children_with_numeric_keys()
    {
        $node = new Node(['id' => 1, 'name' => 'foobar']);
        $node->addChildren([$child = new Node(['id' => 2, 'name' => 'first-child'])]);
        $child->addChildren([$child2 = new Node(['id' => 3, 'name' => 'second-child'])]);

        $this->assertEquals([
            1 => 'foobar',
            2 => 'first-child',
            3 => 'second-child',
        ],$node->pluck('id','name'));
    }

    /** @test */
    function it_can_pluck_from_ancestors()
    {
        $node = new Node(['id' => 1, 'name' => 'foobar']);
        $node->addChildren([$child = new Node(['id' => 2, 'name' => 'first-child'])]);
        $child->addChildren([$child2 = new Node(['id' => 3, 'name' => 'second-child'])]);

        $this->assertEquals([
            3,2,1
        ],$child2->pluckAncestors('id'));
    }

    /** @test */
    function it_can_pluck_specific_values_of_collection()
    {
        $collection = new NodeCollection(
            new Node(['id' => 1]),
            new Node(['id' => 2]),
            new Node(['id' => 3])
        );

        $this->assertEquals([
            1,2,3
        ],$collection->pluck('id'));
    }
}