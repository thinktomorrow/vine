<?php

namespace Thinktomorrow\Vine\Tests;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\DefaultNode;
use Thinktomorrow\Vine\NodeCollection;
use Thinktomorrow\Vine\Tests\Fixtures\CustomNodeCollection;

class NodeCollectionCreateTest extends TestCase
{
    public function test_it_accepts_variadic_array_of_nodes()
    {
        $collection = new NodeCollection([
            new DefaultNode('foobar'),
            new DefaultNode('foobar-2'),
        ]);

        $this->assertCount(2, $collection->all());
    }

    public function test_it_accepts_a_custom_create_callable()
    {
        $collection = NodeCollection::fromIterable([
            new DefaultNode(['id' => 1]),
            new DefaultNode(['id' => 2]),
        ], function ($entry) {
            return new DefaultNode(['id' => $entry->getNodeValue('id') * 3]);
        });

        $this->assertCount(2, $collection->all());
        $this->assertEquals(3, $collection->first()->getNodeId());
        $this->assertEquals(6, $collection[1]->getNodeId());
    }

    public function test_it_accepts_an_array()
    {
        $collection = NodeCollection::fromArray([
            ['id' => 1, 'parent_id' => null],
            ['id' => 2, 'parent_id' => 1],
        ]);

        $this->assertEquals(2, $collection->total());
        $this->assertCount(1, $collection->all());
    }

    public function test_it_accepts_an_array_of_nodes()
    {
        $collection = NodeCollection::fromArray([
            new DefaultNode(['id' => 1]),
            new DefaultNode(['id' => 2, 'parent_id' => 1]),
        ]);

        $this->assertEquals(2, $collection->total());
        $this->assertCount(1, $collection->all());
    }

    public function test_it_accepts_an_array_of_nodes_with_invalid_parent_id()
    {
        $collection = NodeCollection::fromArray([
            new DefaultNode(['id' => 1]),
            new DefaultNode(['id' => 2, 'parent_id' => 3]),
        ]);

        $this->assertEquals(2, $collection->total());
        $this->assertCount(2, $collection->all());
    }

    public function test_it_should_not_reference_itself_as_parent()
    {
        $collection = NodeCollection::fromArray([
            new DefaultNode(['id' => 1]),
            new DefaultNode(['id' => 2, 'parent_id' => 2]),
        ]);

        // Is not added because invalid
        $this->assertEquals(1, $collection->total());
        $this->assertCount(1, $collection->all());
    }

    public function test_it_can_set_custom_id_or_parent_id_references()
    {
        $collection = NodeCollection::fromIterable([
            ['key' => 'foobar', 'parent_key' => null],
            ['key' => 'foobar-2', 'parent_key' => 'foobar'],
        ], function ($entry) {
            return new DefaultNode($entry, new NodeCollection(), 'key', 'parent_key');
        });

        $this->assertEquals(2, $collection->total());
        $this->assertCount(1, $collection->all());
    }

    public function test_it_can_set_array_keys_as_id_or_parent_id_references()
    {
        $collection = NodeCollection::fromIterable([
            ['foobar', null],
            ['foobar-2', 'foobar'],
        ], function ($entry) {
            return new DefaultNode($entry, new NodeCollection(), '0', '1');
        });

        $this->assertEquals(2, $collection->total());
        $this->assertCount(1, $collection->all());
    }

    public function test_it_can_use_custom_node_collection()
    {
        $collection = CustomNodeCollection::fromIterable([
            ['key' => 'foobar', 'parent_key' => null],
            ['key' => 'foobar-2', 'parent_key' => 'foobar'],
            ['key' => 'foobar-3', 'parent_key' => 'foobar-2'],
        ], function ($entry) {
            return new DefaultNode($entry, new CustomNodeCollection(), 'key', 'parent_key');
        });

        $this->assertInstanceOf(CustomNodeCollection::class, $collection);
        $this->assertInstanceOf(CustomNodeCollection::class, $collection->first()->getChildNodes());
        $this->assertInstanceOf(CustomNodeCollection::class, $collection->first()->getChildNodes()->first()->getChildNodes());
    }

    public function test_it_must_create_node()
    {
        $this->expectException(\InvalidArgumentException::class);

        NodeCollection::fromIterable([
            ['foobar', null],
        ], function ($entry) {
            return $entry;
        });
    }
}
