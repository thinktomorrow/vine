<?php

namespace Thinktomorrow\Vine\Tests;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\DefaultNode;
use Thinktomorrow\Vine\Node;
use Thinktomorrow\Vine\NodeCollection;
use Thinktomorrow\Vine\NodeCollectionFactory;

class DefaultNodeCollectionTest extends TestCase
{
    public function test_it_can_find_many_nodes_by_their_primary_identifiers()
    {
        $nodes = $this->getTree()->findMany('id', [5, 2]);

        $this->assertInstanceOf(NodeCollection::class, $nodes);
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
        $collection = new NodeCollection([
            new DefaultNode(['id' => 1]),
        ]);

        $collection->add(
            new DefaultNode(['id' => 2]),
            new DefaultNode(['id' => 3])
        );

        $this->assertCount(3, $collection->all());
    }

    public function test_it_can_get_total_count_of_all_nodes_and_children()
    {
        $collection = new NodeCollection([
            (new DefaultNode(['id' => 1]))
                ->addChildNodes(
                    (new DefaultNode(['id' => 2]))
                        ->addChildNodes(new DefaultNode(['id' => 3]))
                ),
            new DefaultNode(['id' => 4]),
        ]);

        $this->assertEquals(4, $collection->total());
        $this->assertEquals(2, $collection->first()->getChildNodes()->total());
    }

    public function test_it_can_change_each_child_node_with_a_callback()
    {
        $original = new DefaultNode((object) ['id' => 2]);
        $original->addChildNodes([new DefaultNode(['id' => '23']), new DefaultNode(['id' => '22']), new DefaultNode(['id' => '21'])]);

        $original->getChildNodes()->map(function (Node $node) {
            $entry = $node->getNodeEntry();
            $entry['title'] = 'new';
            $node->replaceNodeEntry($entry);

            return $node;
        });

        $expected = new DefaultNode((object) ['id' => 2]);
        $expected->addChildNodes([new DefaultNode(['id' => '23', 'title' => 'new']), new DefaultNode(['id' => '22', 'title' => 'new']), new DefaultNode(['id' => '21', 'title' => 'new'])]);

        $this->assertEquals($expected, $original);
    }

    public function test_it_can_map_all_child_nodes_recursively()
    {
        $original = new DefaultNode((object) ['id' => 2]);
        $original->addChildNodes([(new DefaultNode(['id' => '23']))->addChildNodes(new DefaultNode(['id' => '24'])), new DefaultNode(['id' => '22']), new DefaultNode(['id' => '21'])]);

        $original->getChildNodes()->mapRecursive(function ($node) {
            $entry = $node->getNodeEntry();
            $entry['title'] = 'new';
            $node->replaceNodeEntry($entry);

            return $node;
        });

        $expected = new DefaultNode((object) ['id' => 2]);
        $expected->addChildNodes([(new DefaultNode(['id' => '23', 'title' => 'new']))->addChildNodes(new DefaultNode(['id' => '24', 'title' => 'new'])), new DefaultNode(['id' => '22', 'title' => 'new']), new DefaultNode(['id' => '21', 'title' => 'new'])]);

        $this->assertEquals($expected, $original);
    }

    public function test_it_can_loop_each_direct_child_node_with_a_callback()
    {
        $original = new DefaultNode((object) ['id' => 2]);
        $original->addChildNodes([new DefaultNode(['id' => '23']), new DefaultNode(['id' => '22']), new DefaultNode(['id' => '21'])]);

        $original->getChildNodes()->each(function (DefaultNode $node) {
            $entry = $node->getNodeEntry();
            $entry['title'] = 'new';
            $node->replaceNodeEntry($entry);

            return $node;
        });

        $expected = new DefaultNode((object) ['id' => 2]);
        $expected->addChildNodes([new DefaultNode(['id' => '23', 'title' => 'new']), new DefaultNode(['id' => '22', 'title' => 'new']), new DefaultNode(['id' => '21', 'title' => 'new'])]);

        $this->assertEquals($expected, $original);
    }

    public function test_it_can_loop_all_child_nodes_recursively()
    {
        $original = new DefaultNode((object) ['id' => 2]);
        $original->addChildNodes([(new DefaultNode(['id' => '23']))->addChildNodes(new DefaultNode(['id' => '24'])), new DefaultNode(['id' => '22']), new DefaultNode(['id' => '21'])]);

        $original->getChildNodes()->eachRecursive(function ($node) {
            $entry = $node->getNodeEntry();
            $entry['title'] = 'new';
            $node->replaceNodeEntry($entry);

            return $node;
        });

        $expected = new DefaultNode((object) ['id' => 2]);
        $expected->addChildNodes([(new DefaultNode(['id' => '23', 'title' => 'new']))->addChildNodes(new DefaultNode(['id' => '24', 'title' => 'new'])), new DefaultNode(['id' => '22', 'title' => 'new']), new DefaultNode(['id' => '21', 'title' => 'new'])]);

        $this->assertEquals($expected, $original);
    }

    private function getTree(): NodeCollection
    {
        $records = [
            ['id' => 1, 'name' => 'foobar', 'parent_id' => 0],
            ['id' => 2, 'name' => 'foobar2', 'parent_id' => 1],
            ['id' => 3, 'name' => 'foobar3', 'parent_id' => 1],
            ['id' => 5, 'name' => 'foobar5', 'parent_id' => 3],
            ['id' => 4, 'name' => 'foobar4', 'parent_id' => 0],
        ];

        return (new NodeCollectionFactory())->fromIterable(new NodeCollection(), $records, function ($record) {
            return new DefaultNode($record);
        });
    }
}
