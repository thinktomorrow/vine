<?php

namespace Thinktomorrow\Vine\Tests\Commands;

use Thinktomorrow\Vine\DefaultNode;
use Thinktomorrow\Vine\Node;
use Thinktomorrow\Vine\NodeCollection;

class RemoveTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function it_can_remove_self_from_parent()
    {
        $node = new DefaultNode(null);
        $node->addChildNodes([$child = new DefaultNode(null)]);
        $child->addChildNodes([$child2 = new DefaultNode(null)]);

        $child->getParentNode()->removeNode($child);

        $this->assertCount(0, $node->getChildNodes());
        $this->assertCount(1, $child->getChildNodes());
        $this->assertNull($child->getParentNode());

        // Assert parent still exists
        $this->assertInstanceOf(Node::class, $node);
    }

    /** @test */
    public function it_can_remove_nodes()
    {
        $collection = new \Thinktomorrow\Vine\NodeCollection([
            $child = new DefaultNode(['id' => 1, 'name' => 'foobar']),
            $child2 = new DefaultNode(['id' => 2, 'name' => 'foobar-2']),
            $child3 = new DefaultNode(['id' => 3, 'name' => 'foobar-3']),
        ]);

        $collection->removeNode($child);

        $this->assertCount(2, $collection->all());
        $this->assertSame($child2, $collection->find('id', 2));
        $this->assertSame($child3, $collection->find('id', 3));
        $this->assertNull($collection->find('id', 1));
    }

    /** @test */
    public function it_can_remove_nested_nodes()
    {
        $root = new DefaultNode(['id' => 1, 'name' => 'foobar']);
        $root->addChildNodes($child2 = new DefaultNode(['id' => 2, 'name' => 'foobar-2']));
        $child2->addChildNodes($child4 = new DefaultNode(['id' => 4, 'name' => 'foobar-4']));

        $child2->addChildNodes($child3 = new DefaultNode(['id' => 3, 'name' => 'foobar-3']));
        $child3->addChildNodes($child5 = new DefaultNode(['id' => 5, 'name' => 'foobar-5']));

        $root->removeNode($child3);

        $this->assertEquals(2, $root->getTotalChildNodesCount());
        $this->assertSame($child2, $root->getChildNodes()->find('id', 2));
        $this->assertSame($child4, $root->getChildNodes()->find('id', 4));
        $this->assertNull($root->getChildNodes()->find('id', 3));
        $this->assertNull($root->getChildNodes()->find('id', 5));
    }

    /** @test */
    public function when_node_is_removed_all_children_are_removed_as_well()
    {
        $root = new DefaultNode(['id' => 1, 'name' => 'foobar']);
        $root->addChildNodes($child1 = new DefaultNode(['id' => 2, 'name' => 'foobar-2']));
        $child1->addChildNodes($child2 = new DefaultNode(['id' => 3, 'name' => 'foobar-3']));
        $child2->addChildNodes($child3 = new DefaultNode(['id' => 4, 'name' => 'foobar-4']));

        $collection = new NodeCollection([$root]);
        $cleanCollection = $collection->removeNode($child1);

        $this->assertEquals(1, $cleanCollection->total());
        $this->assertCount(0, $root->getChildNodes());
    }

    /** @test */
    public function when_a_node_is_removed_via_callback_all_children_are_removed_as_well()
    {
        $root = new DefaultNode(['id' => 1, 'name' => 'foobar']);
        $root->addChildNodes($child1 = new DefaultNode(['id' => 2, 'name' => 'foobar-2']));
        $child1->addChildNodes($child2 = new DefaultNode(['id' => 3, 'name' => 'foobar-3']));
        $child2->addChildNodes($child3 = new DefaultNode(['id' => 4, 'name' => 'foobar-4']));

        $collection = new NodeCollection([$root]);
        $cleanCollection = $collection->remove(fn ($node) => $node->getNodeValue('id') == 2);

        // Original tree remains intact
        $this->assertEquals(4, $collection->total());
        $this->assertCount(1, $root->getChildNodes());

        $this->assertEquals(1, $cleanCollection->total());
        $this->assertCount(0, $cleanCollection->first()->getChildNodes());
    }

    /** @test */
    public function node_can_be_removed_from_collection()
    {
        $node = new DefaultNode(1);
        $node2 = new DefaultNode(2);
        $node->addChildNodes([$child = new DefaultNode(3)]);
        $node2->addChildNodes([$child3 = new DefaultNode(4)]);

        $collection = new \Thinktomorrow\Vine\NodeCollection([
            $node,
            $node2,
        ]);

        $collection->removeNode($child3);

        $this->assertEquals(3, $collection->total());
        $this->assertCount(0, $node2->getChildNodes());
        $this->assertNotNull($child3->getParentNode());
    }

    /** @test */
    public function individual_node_removal_is_not_immutable()
    {
        $node = new DefaultNode(1);
        $node->addChildNodes([$child = new DefaultNode(3)]);

        $collection = new \Thinktomorrow\Vine\NodeCollection([
            $node,
        ]);

        $removedCollection = $collection->removeNode($child);

        $this->assertEquals($collection, $removedCollection);
        $this->assertEquals($collection->first(), $removedCollection->first());
    }
}
