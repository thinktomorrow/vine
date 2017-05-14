<?php

use PHPUnit\Framework\TestCase;
use Vine\Node;

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
}