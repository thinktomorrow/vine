<?php

use Vine\Node;

class RemoveTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function it_can_remove_self_from_parent()
    {
        $node = new Node(null);
        $node->addChildren([$child = new Node(null)]);
        $child->addChildren([$child2 = new Node(null)]);

        $child->remove();

        $this->assertCount(0, $node->getChildren());
        $this->assertCount(1, $child->getChildren());
        $this->assertNull($child->parent());

        // Assert parent still exists
        $this->assertInstanceOf(Node::class, $node);
    }

    /** @test */
    public function it_can_remove_nodes()
    {
        $collection = new \Vine\NodeCollection(
            $child = new Node(['id' => 1, 'name' => 'foobar']),
            $child2 = new Node(['id' => 2, 'name' => 'foobar-2']),
            $child3 = new Node(['id' => 3, 'name' => 'foobar-3'])
        );

        $collection->remove($child);

        $this->assertCount(2, $collection->all());
        $this->assertSame($child2, $collection->find('id', 2));
        $this->assertSame($child3, $collection->find('id', 3));
        $this->assertNull($collection->find('id', 1));
    }

    /** @test */
    public function it_can_remove_nested_nodes()
    {
        $root = new Node(['id' => 1, 'name' => 'foobar']);
        $root->addChildren($child2 = new Node(['id' => 2, 'name' => 'foobar-2']));
        $child2->addChildren($child4 = new Node(['id' => 4, 'name' => 'foobar-4']));

        $child2->addChildren($child3 = new Node(['id' => 3, 'name' => 'foobar-3']));
        $child3->addChildren($child5 = new Node(['id' => 5, 'name' => 'foobar-5']));

        $root->remove($child3);

        $this->assertEquals(2, $root->total());
        $this->assertSame($child2, $root->getChildren()->find('id', 2));
        $this->assertSame($child4, $root->getChildren()->find('id', 4));
        $this->assertNull($root->getChildren()->find('id', 3));
        $this->assertNull($root->getChildren()->find('id', 5));
    }

    /** @test */
    public function node_can_be_removed_from_collection()
    {
        $node = new Node(1);
        $node2 = new Node(2);
        $node->addChildren([$child = new Node(3)]);
        $node2->addChildren([$child3 = new Node(4)]);

        $collection = new \Vine\NodeCollection(
            $node, $node2
        );

        $collection->remove($child3);

        $this->assertEquals(3, $collection->total());
        $this->assertCount(0, $node2->getChildren());
        $this->assertNotNull($child3->parent());
    }
}
