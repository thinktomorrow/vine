<?php

use PHPUnit\Framework\TestCase;
use Vine\Node;
use Vine\NodeCollection;

class NodeTest extends TestCase
{
    /** @test */
    public function it_can_add_an_entry_value_to_a_node()
    {
        $node = new Node('foobar');
        $this->assertEquals('foobar', $node->entry());

        $node = new Node($entry = (object) ['foo' => 'bar']);
        $this->assertSame($entry, $node->entry());
    }

    /** @test */
    public function it_can_add_children_to_a_node()
    {
        $node = new Node('foobar');
        $this->assertEmpty($node->children());

        $this->assertCount(2, $node->addChildren([
            new Node('first-child'),
            new Node('second-child'),
        ])->children());
    }

    /** @test */
    public function an_added_child_must_be_node_or_collection()
    {
        $this->expectException(InvalidArgumentException::class);

        $node = new Node('foobar');
        $node->addChildren('string is not allowed');
    }

    /** @test */
    public function it_can_add_children_to_a_node_consecutively()
    {
        $node = new Node('foobar');
        $this->assertEmpty($node->children());

        $node->addChildren([new Node('first-child')]);

        $this->assertCount(2, $node->addChildren([
            new Node('second-child'),
        ])->children());
    }

    /** @test */
    public function by_setting_children_the_parent_is_set_for_each_one()
    {
        $node = new Node('foobar');
        $node->addChildren([$child = new Node('first-child')]);

        $this->assertSame($node, $child->parent());
    }

    /** @test */
    public function it_can_verify_a_node_is_equal()
    {
        $first = new Node('foobar');
        $second = $first;

        $this->assertTrue($first->equals($second));
    }

    /** @test */
    public function a_node_is_a_leaf_if_it_has_no_children()
    {
        $node = new Node('foobar');
        $node->addChildren([$child = new Node('first-child')]);

        $this->assertFalse($node->isLeaf());
        $this->assertTrue($child->isLeaf());
    }

    /** @test */
    public function a_node_is_a_root_if_it_has_no_parent()
    {
        $node = new Node('foobar');
        $node->addChildren([$child = new Node('first-child')]);

        $this->assertTrue($node->isRoot());
        $this->assertFalse($child->isRoot());
    }

    /** @test */
    public function it_can_get_depth()
    {
        $node = new Node(null);
        $node->addChildren([$child = new Node(null)]);
        $child->addChildren([$child2 = new Node(null)]);

        $this->assertEquals(2, $child2->depth());
        $this->assertEquals(1, $child->depth());
        $this->assertEquals(0, $node->depth());
    }

    /** @test */
    public function it_can_get_total_count_of_all_children()
    {
        $node = new Node(null);
        $node->addChildren([$child = new Node(null)]);
        $child->addChildren([$child2 = new Node(null)]);

        $this->assertEquals(0, $child2->total());
        $this->assertEquals(1, $child->total());
        $this->assertEquals(2, $node->total());
    }

    /** @test */
    public function it_can_remove_self_from_parent()
    {
        $node = new Node(null);
        $node->addChildren([$child = new Node(null)]);
        $child->addChildren([$child2 = new Node(null)]);

        $child->remove();

        $this->assertCount(0, $node->children());
        $this->assertCount(1, $child->children());
        $this->assertNull($child->parent());

        // Assert parent still exists
        $this->assertInstanceOf(Node::class, $node);
    }

    /** @test */
    public function it_can_replace_entry()
    {
        $node = new Node('foobar');
        $node->replaceEntry('berry');

        $this->assertEquals('berry', $node->entry());
    }

    /** @test */
    public function it_can_fetch_entry_values_via_node()
    {
        $node = new Node(['id' => 1, 'label' => 'foobar']);

        $this->assertEquals(1, $node->id);
        $this->assertEquals('foobar', $node->label);
    }

    /** @test */
    public function it_can_fetch_children_as_property_call()
    {
        $node = new Node(null);
        $node->addChildren([$child = new Node(null)]);

        $this->assertInstanceOf(NodeCollection::class, $node->children);
        $this->assertSame($child, $node->children->first());
    }

    /** @test */
    public function it_can_check_if_it_has_children()
    {
        $node = new Node(null);
        $node->addChildren([$child = new Node(null)]);
        $child->addChildren([$child2 = new Node(null)]);

        $this->assertTrue($node->hasChildren());
        $this->assertFalse($child2->hasChildren());
    }

    /** @test */
    public function it_forwards_call_to_entry_method()
    {
        $node = new Node(new CustomEntry());

        $this->assertEquals('foobar', $node->url());
    }

    /** @test */
    public function non_found_method_results_in_exception()
    {
        $this->expectException(InvalidArgumentException::class);
        (new Node([]))->unknown();
    }
}

class CustomEntry
{
    public function url()
    {
        return 'foobar';
    }
}
