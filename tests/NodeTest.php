<?php

use PHPUnit\Framework\TestCase;
use Vine\Node;
use Vine\NodeCollection;

class NodeTest extends TestCase
{
    /** @test */
    function it_can_assign_an_entry_value_to_a_node()
    {
        $node = new Node('foobar');
        $this->assertEquals('foobar',$node->entry());

        $node = new Node($entry = (object)['foo' => 'bar']);
        $this->assertSame($entry,$node->entry());
    }

    /** @test */
    function it_can_assign_children_to_a_node()
    {
        $node = new Node('foobar');
        $this->assertEmpty($node->children());

        $this->assertCount(2,$node->addChildren([
            new Node('first-child'),
            new Node('second-child')
        ])->children());
    }

    /** @test */
    function it_can_add_children_to_a_node_consecutively()
    {
        $node = new Node('foobar');
        $this->assertEmpty($node->children());

        $node->addChildren([new Node('first-child')]);

        $this->assertCount(2,$node->addChildren([
            new Node('second-child')
        ])->children());
    }

    /** @test */
    function by_setting_children_the_parent_is_set_for_each_one()
    {
        $node = new Node('foobar');
        $node->addChildren([$child = new Node('first-child')]);

        $this->assertSame($node,$child->parent());
    }

    /** @test */
    function a_node_is_a_leaf_if_it_has_no_children()
    {
        $node = new Node('foobar');
        $node->addChildren([$child = new Node('first-child')]);

        $this->assertFalse($node->isLeaf());
        $this->assertTrue($child->isLeaf());
    }

    /** @test */
    function a_node_is_a_root_if_it_has_no_parent()
    {
        $node = new Node('foobar');
        $node->addChildren([$child = new Node('first-child')]);

        $this->assertTrue($node->isRoot());
        $this->assertFalse($child->isRoot());
    }

    /** @test */
    function it_can_get_depth()
    {
        $node = new Node(null);
        $node->addChildren([$child = new Node(null)]);
        $child->addChildren([$child2 = new Node(null)]);

        $this->assertEquals(2,$child2->depth());
        $this->assertEquals(1,$child->depth());
        $this->assertEquals(0,$node->depth());
    }

    /** @test */
    function it_can_get_count_of_all_children()
    {
        $node = new Node(null);
        $node->addChildren([$child = new Node(null)]);
        $child->addChildren([$child2 = new Node(null)]);

        $this->assertEquals(0,$child2->count());
        $this->assertEquals(1,$child->count());
        $this->assertEquals(2,$node->count());
    }

    /** @test */
    function it_can_find_many_nodes_by_their_primary_identifiers()
    {
        $node = new Node(['id' => 1]);
        $node->addChildren([$child = new Node(['id' => 2])]);
        $child->addChildren([$child2 = new Node(['id' => 3])]);

        // Not part of the result set
        $child->addChildren([$child3 = new Node(['id' => 4])]);
        $node->addChildren([$child4 = new Node(['id' => 5])]);

        $nodes = $node->findMany('id',[2,3]);

        $this->assertInstanceOf(NodeCollection::class, $nodes);
        $this->assertCount(2,$nodes);
        $this->assertSame($child, $nodes[0]);
        $this->assertSame($child2, $nodes[1]);
    }

    /** @test */
    function it_can_find_a_node_by_its_primary_identifier()
    {
        $node = new Node(['id' => 1]);
        $node->addChildren([$child = new Node(['id' => 2])]);
        $child->addChildren([$child2 = new Node(['id' => 3])]);

        $result = $node->find('id',2);

        $this->assertInstanceOf(Node::class, $result);
        $this->assertSame($child, $result);
    }

    /** @test */
    function it_can_detach_parent()
    {
        $node = new Node(null);
        $node->addChildren([$child = new Node(null)]);
        $child->addChildren([$child2 = new Node(null)]);

        $child->detachParent();

        $this->assertCount(0,$node->children());
        $this->assertCount(1,$child->children());
        $this->assertNull($child->parent());

        // Assert parent still exists
        $this->assertInstanceOf(Node::class,$node);
    }

}