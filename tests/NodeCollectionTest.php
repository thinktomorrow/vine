<?php

namespace Thinktomorrow\Vine\Tests;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\DefaultNode;
use Thinktomorrow\Vine\NodeCollection;
use Thinktomorrow\Vine\Sources\ArraySource;

class NodeCollectionTest extends TestCase
{
    /** @test */
    public function it_contains_array_of_nodes()
    {
        $collection = new NodeCollection();

        $this->assertIsArray($collection->all());
        $this->assertCount(0, $collection->all());
    }

    /** @test */
    public function it_accepts_variadic_array_of_nodes()
    {
        $collection = new NodeCollection(
            new DefaultNode('foobar'),
            new DefaultNode('foobar-2')
        );

        $this->assertCount(2, $collection->all());
    }

    /** @test */
    public function it_accepts_an_array_of_nodes()
    {
        $collection = NodeCollection::fromArray([
            new DefaultNode(['id' => 1]),
            new DefaultNode(['id' => 2]),
        ]);

        $this->assertCount(2, $collection->all());
    }

    /** @test */
    public function it_accepts_a_custom_source()
    {
        $collection = NodeCollection::fromSource(new ArraySource([
            new DefaultNode(['id' => 1]),
            new DefaultNode(['id' => 2]),
        ]));

        $this->assertCount(2, $collection->all());
    }

    /** @test */
    public function it_can_add_array_of_nodes()
    {
        $collection = new NodeCollection(
            new DefaultNode(['id' => 1])
        );

        $collection->add(
            new DefaultNode(['id' => 2]),
            new DefaultNode(['id' => 3])
        );

        $this->assertCount(3, $collection->all());
    }

    /** @test */
    public function it_can_get_total_count_of_all_nodes_and_children()
    {
        $collection = new NodeCollection(
            (new DefaultNode(['id' => 1]))
                ->addChildNodes(
                    (new DefaultNode(['id' => 2]))
                ->addChildNodes(new DefaultNode(['id' => 3]))
                ),
            new DefaultNode(['id' => 4])
        );

        $this->assertEquals(4, $collection->total());
        $this->assertEquals(2, $collection->first()->getChildNodes()->total());
    }

    /** @test */
    public function it_can_change_each_child_node_with_a_callback()
    {
        $original = new DefaultNode((object) ['id' => 2]);
        $original->addChildNodes([new DefaultNode(['id' => '23']), new DefaultNode(['id' => '22']), new DefaultNode(['id' => '21'])]);

        $original->getChildNodes()->map(function ($node) {
            $entry = $node->getNodeEntry();
            $entry['title'] = 'new';

            return $node->replaceNodeEntry($entry);
        });

        $expected = new DefaultNode((object) ['id' => 2]);
        $expected->addChildNodes([new DefaultNode(['id' => '23', 'title' => 'new']), new DefaultNode(['id' => '22', 'title' => 'new']), new DefaultNode(['id' => '21', 'title' => 'new'])]);

        $this->assertEquals($expected, $original);
    }

    /** @test */
    public function it_can_map_all_child_nodes_recursively()
    {
        $original = new DefaultNode((object) ['id' => 2]);
        $original->addChildNodes([(new DefaultNode(['id' => '23']))->addChildNodes(new DefaultNode(['id' => '24'])), new DefaultNode(['id' => '22']), new DefaultNode(['id' => '21'])]);

        $original->getChildNodes()->mapRecursive(function ($node) {
            $entry = $node->getNodeEntry();
            $entry['title'] = 'new';

            return $node->replaceNodeEntry($entry);
        });

        $expected = new DefaultNode((object) ['id' => 2]);
        $expected->addChildNodes([(new DefaultNode(['id' => '23', 'title' => 'new']))->addChildNodes(new DefaultNode(['id' => '24', 'title' => 'new'])), new DefaultNode(['id' => '22', 'title' => 'new']), new DefaultNode(['id' => '21', 'title' => 'new'])]);

        $this->assertEquals($expected, $original);
    }
}
