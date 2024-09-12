<?php

namespace Thinktomorrow\Vine\Tests;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\Node;
use Thinktomorrow\Vine\NodeCollection;
use Thinktomorrow\Vine\Tests\Fixtures\CustomModelNode;
use Thinktomorrow\Vine\Tests\Fixtures\CustomNodeCollection;

class NodeCollectionTest extends TestCase
{
    public function test_it_contains_array_of_nodes()
    {
        $collection = new NodeCollection();

        $this->assertIsArray($collection->all());
        $this->assertCount(0, $collection->all());
    }

    public function test_it_can_find_many_nodes_by_their_primary_identifiers()
    {
        $nodes = $this->getTree()->findMany('id', [5, 2]);

        $this->assertInstanceOf(NodeCollection::class, $nodes);
        $this->assertInstanceOf(CustomNodeCollection::class, $nodes);
    }

    public function test_it_can_find_a_node_by_its_primary_identifier()
    {
        $tree = $this->getTree();
        $node = $tree->find('id', 5);

        $this->assertSame($node, $tree[0]->getChildNodes()[1]->getChildNodes()[0]);
    }

    public function test_it_can_find_a_node_by_its_node_id()
    {
        $tree = $this->getTree();
        $node = $tree->findById(5);

        $this->assertSame($node, $tree[0]->getChildNodes()[1]->getChildNodes()[0]);
    }

    public function test_it_can_get_total_of_nodes()
    {
        $this->assertEquals(5, $this->getTree()->total());
    }

    public function test_it_can_add_array_of_nodes()
    {
        $collection = new CustomNodeCollection([
            new CustomModelNode(1),
        ]);

        $collection->add(
            new CustomModelNode(2),
            new CustomModelNode(3)
        );

        $this->assertCount(3, $collection->all());
    }

    public function test_it_can_get_total_count_of_all_nodes_and_children()
    {
        $collection = new NodeCollection([
            (new CustomModelNode(1))
                ->addChildNodes(
                    (new CustomModelNode(2))
                        ->addChildNodes(new CustomModelNode(3))
                ),
            new CustomModelNode(4),
        ]);

        $this->assertEquals(4, $collection->total());
        $this->assertEquals(2, $collection->first()->getChildNodes()->total());
    }

    public function test_it_can_change_each_child_node_with_a_callback()
    {
        $original = new CustomModelNode(2);
        $original->addChildNodes([new CustomModelNode(23), new CustomModelNode(22), new CustomModelNode(21)]);

        $original->getChildNodes()->map(function (Node $node) {
            $node->changeValue('title', 'new');

            return $node;
        });

        $expected = new CustomModelNode(2);
        $expected->addChildNodes([new CustomModelNode(23, null, ['title' => 'new']), new CustomModelNode(22, null, ['title' => 'new']), new CustomModelNode(21, null, ['title' => 'new'])]);

        $this->assertEquals($expected, $original);
    }

    public function test_it_can_map_all_child_nodes_recursively()
    {
        $original = new CustomModelNode(2);
        $original->addChildNodes([(new CustomModelNode(23))->addChildNodes(new CustomModelNode(24)), new CustomModelNode(22), new CustomModelNode(21)]);

        $original->getChildNodes()->mapRecursive(function ($node) {
            $node->changeValue('title', 'new');

            return $node;
        });

        $expected = new CustomModelNode(2);
        $expected->addChildNodes([(new CustomModelNode(23, null, ['title' => 'new']))->addChildNodes(new CustomModelNode(24, null, ['title' => 'new'])), new CustomModelNode(22, null, ['title' => 'new']), new CustomModelNode(21, null, ['title' => 'new'])]);

        $this->assertEquals($expected, $original);
    }

    public function test_it_can_loop_each_direct_child_node_with_a_callback()
    {
        $original = new CustomModelNode(2);
        $original->addChildNodes([new CustomModelNode(23), new CustomModelNode(22), new CustomModelNode(21)]);

        $original->getChildNodes()->each(function (CustomModelNode $node) {
            $node->changeValue('title', 'new');

            return $node;
        });

        $expected = new CustomModelNode(2);
        $expected->addChildNodes([new CustomModelNode(23, null, ['title' => 'new']), new CustomModelNode(22, null, ['title' => 'new']), new CustomModelNode(21, null, ['title' => 'new'])]);

        $this->assertEquals($expected, $original);
    }

    public function test_it_can_loop_all_child_nodes_recursively()
    {
        $original = new CustomModelNode(2);
        $original->addChildNodes([(new CustomModelNode(23))->addChildNodes(new CustomModelNode(24)), new CustomModelNode(22), new CustomModelNode(21)]);

        $original->getChildNodes()->eachRecursive(function ($node) {
            $node->changeValue('title', 'new');

            return $node;
        });

        $expected = new CustomModelNode(2);
        $expected->addChildNodes([(new CustomModelNode(23, null, ['title' => 'new']))->addChildNodes(new CustomModelNode(24, null, ['title' => 'new'])), new CustomModelNode(22, null, ['title' => 'new']), new CustomModelNode(21, null, ['title' => 'new'])]);

        $this->assertEquals($expected, $original);
    }

    private function getTree(): CustomNodeCollection
    {
        $records = [
            ['id' => 1, 'name' => 'foobar', 'parent_id' => 0],
            ['id' => 2, 'name' => 'foobar2', 'parent_id' => 1],
            ['id' => 3, 'name' => 'foobar3', 'parent_id' => 1],
            ['id' => 5, 'name' => 'foobar5', 'parent_id' => 3],
            ['id' => 4, 'name' => 'foobar4', 'parent_id' => 0],
        ];

        return CustomNodeCollection::fromIterable($records, function ($record) {
            return new CustomModelNode($record['id'], $record['parent_id'], $record);
        });
    }
}
