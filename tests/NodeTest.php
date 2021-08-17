<?php

namespace Thinktomorrow\Vine\Tests;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\DefaultNode;
use Thinktomorrow\Vine\NodeCollection;

class NodeTest extends TestCase
{
    /** @test */
    public function it_can_add_an_entry_value_to_a_node()
    {
        $node = new DefaultNode('foobar');
        $this->assertEquals('foobar', $node->getNodeEntry());

        $node = new DefaultNode($entry = (object) ['foo' => 'bar']);
        $this->assertSame($entry, $node->getNodeEntry());
    }

    /** @test */
    public function it_can_add_children_to_a_node()
    {
        $node = new DefaultNode('foobar');
        $this->assertEmpty($node->getChildNodes());

        $this->assertCount(2, $node->addChildNodes([
            new DefaultNode('first-child'),
            new DefaultNode('second-child'),
        ])->getChildNodes());
    }

    /** @test */
    public function an_added_child_must_be_node_or_collection()
    {
        $this->expectException(\InvalidArgumentException::class);

        $node = new DefaultNode('foobar');
        $node->addChildNodes('string is not allowed');
    }

    /** @test */
    public function it_can_add_children_to_a_node_consecutively()
    {
        $node = new DefaultNode('foobar');
        $this->assertEmpty($node->getChildNodes());

        $node->addChildNodes([new DefaultNode('first-child')]);

        $this->assertCount(2, $node->addChildNodes([
            new DefaultNode('second-child'),
        ])->getChildNodes());
    }

    /** @test */
    public function by_setting_children_the_parent_is_set_for_each_one()
    {
        $node = new DefaultNode('foobar');
        $node->addChildNodes([$child = new DefaultNode('first-child')]);

        $this->assertSame($node, $child->getParentNode());
    }

    /** @test */
    public function it_can_verify_a_node_is_equal()
    {
        $first = new DefaultNode('foobar');
        $second = $first;

        $this->assertTrue($first->equalsNode($second));
    }

    /** @test */
    public function a_node_is_a_leaf_if_it_has_no_children()
    {
        $node = new DefaultNode('foobar');
        $node->addChildNodes([$child = new DefaultNode('first-child')]);

        $this->assertFalse($node->isLeafNode());
        $this->assertTrue($child->isLeafNode());
    }

    /** @test */
    public function a_node_is_a_root_if_it_has_no_parent()
    {
        $node = new DefaultNode('foobar');
        $node->addChildNodes([$child = new DefaultNode('first-child')]);

        $this->assertTrue($node->isRootNode());
        $this->assertFalse($child->isRootNode());
    }

    /** @test */
    public function it_can_get_depth()
    {
        $node = new DefaultNode(null);
        $node->addChildNodes([$child = new DefaultNode(null)]);
        $child->addChildNodes([$child2 = new DefaultNode(null)]);

        $this->assertEquals(2, $child2->getNodeDepth());
        $this->assertEquals(1, $child->getNodeDepth());
        $this->assertEquals(0, $node->getNodeDepth());
    }

    /** @test */
    public function it_can_get_total_count_of_all_children()
    {
        $node = new DefaultNode(null);
        $node->addChildNodes([$child = new DefaultNode(null)]);
        $child->addChildNodes([$child2 = new DefaultNode(null)]);

        $this->assertEquals(0, $child2->getTotalChildNodesCount());
        $this->assertEquals(1, $child->getTotalChildNodesCount());
        $this->assertEquals(2, $node->getTotalChildNodesCount());
    }

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
        $this->assertInstanceOf(DefaultNode::class, $node);
    }

    /** @test */
    public function it_can_replace_entry()
    {
        $node = new DefaultNode('foobar');
        $node->replaceNodeEntry('berry');

        $this->assertEquals('berry', $node->getNodeEntry());
    }

    /** @test */
    public function it_can_fetch_entry_values_via_node()
    {
        $node = new DefaultNode(['id' => 1, 'label' => 'foobar']);

        $this->assertEquals(1, $node->getNodeEntry('id'));
        $this->assertEquals('foobar', $node->getNodeEntry('label'));
    }

    /** @test */
    public function it_can_fetch_children_as_property_call()
    {
        $node = new DefaultNode(null);
        $node->addChildNodes([$child = new DefaultNode(null)]);

        $this->assertInstanceOf(NodeCollection::class, $node->getChildNodes());
        $this->assertSame($child, $node->getChildNodes()->first());
    }

    /** @test */
    public function it_can_check_if_it_has_children()
    {
        $node = new DefaultNode(null);
        $node->addChildNodes([$child = new DefaultNode(null)]);
        $child->addChildNodes([$child2 = new DefaultNode(null)]);

        $this->assertTrue($node->hasChildNodes());
        $this->assertFalse($child2->hasChildNodes());
    }
}
