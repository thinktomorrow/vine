<?php

namespace Thinktomorrow\Vine\Tests\Queries;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\DefaultNode;
use Thinktomorrow\Vine\NodeCollection;

class PluckTest extends TestCase
{
    public function test_it_can_pluck_specific_values_of_all_children()
    {
        $node = new DefaultNode(['id' => 1, 'name' => 'foobar']);
        $node->addChildNodes([$child = new DefaultNode(['id' => 2, 'name' => 'first-child'])]);
        $child->addChildNodes([$child2 = new DefaultNode(['id' => 3, 'name' => 'second-child'])]);

        $this->assertEquals([
            2, 3,
        ], $node->pluckChildNodes('id'));

        $this->assertEquals([
            1, 2, 3,
        ], $node->pluckChildNodes('id', null, true));
    }

    public function test_it_can_pluck_key_value_pairs_of_all_children()
    {
        $node = new DefaultNode(['id' => 'one', 'name' => 'foobar']);
        $node->addChildNodes([$child = new DefaultNode(['id' => 'two', 'name' => 'first-child'])]);
        $child->addChildNodes([$child2 = new DefaultNode(['id' => 'three', 'name' => 'second-child'])]);

        $this->assertEquals([
            'two' => 'first-child',
            'three' => 'second-child',
        ], $node->pluckChildNodes('id', 'name'));

        $this->assertEquals([
            'one' => 'foobar',
            'two' => 'first-child',
            'three' => 'second-child',
        ], $node->pluckChildNodes('id', 'name', true));
    }

    public function test_it_can_pluck_key_value_pairs_of_all_children_with_numeric_keys()
    {
        $node = new DefaultNode(['id' => 1, 'name' => 'foobar']);
        $node->addChildNodes([$child = new DefaultNode(['id' => 2, 'name' => 'first-child'])]);
        $child->addChildNodes([$child2 = new DefaultNode(['id' => 3, 'name' => 'second-child'])]);

        $this->assertEquals([
            2 => 'first-child',
            3 => 'second-child',
        ], $node->pluckChildNodes('id', 'name'));

        $this->assertEquals([
            1 => 'foobar',
            2 => 'first-child',
            3 => 'second-child',
        ], $node->pluckChildNodes('id', 'name', true));
    }

    public function test_it_can_pluck_from_ancestors()
    {
        $node = new DefaultNode(['id' => 1, 'name' => 'foobar']);
        $node->addChildNodes([$child = new DefaultNode(['id' => 2, 'name' => 'first-child'])]);
        $child->addChildNodes([$child2 = new DefaultNode(['id' => 3, 'name' => 'second-child'])]);

        $this->assertEquals([
            2, 1,
        ], $child2->pluckAncestorNodes('id'));

        $this->assertEquals([
            3, 2, 1,
        ], $child2->pluckAncestorNodes('id', null, true));
    }

    public function test_it_can_pluck_specific_values_of_collection()
    {
        $collection = new NodeCollection([
            new DefaultNode(['id' => 1]),
            new DefaultNode(['id' => 2]),
            new DefaultNode(['id' => 3]),
        ]);

        $this->assertEquals([
            1, 2, 3,
        ], $collection->pluck('id'));
    }

    public function test_it_can_pluck_key_value_pairs_of_collection()
    {
        $collection = new NodeCollection([
            new DefaultNode(['id' => 1, 'label' => 'foobar-1']),
            new DefaultNode(['id' => 3, 'label' => 'foobar-3']),
            new DefaultNode(['id' => 2, 'label' => 'foobar-2']),
        ]);

        $this->assertEquals([
            1 => 'foobar-1',
            3 => 'foobar-3',
            2 => 'foobar-2',
        ], $collection->pluck('id', 'label'));
    }

    public function test_it_can_pluck_methods()
    {
        $collection = new NodeCollection([
            new DefaultNode(['id' => 1]),
            new DefaultNode(['id' => 2]),
            new DefaultNode(['id' => 3]),
        ]);

        $this->assertEquals([
            1, 2, 3,
        ], $collection->pluck('getNodeId'));
    }

    public function test_it_can_pluck_with_closure()
    {
        $collection = new NodeCollection([
            new DefaultNode(['id' => 1]),
            new DefaultNode(['id' => 2]),
            new DefaultNode(['id' => 3]),
        ]);

        $this->assertEquals([
            1, 2, 3,
        ], $collection->pluck(fn ($node) => $node->getNodeId()));
    }
}
