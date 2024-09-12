<?php

namespace Thinktomorrow\Vine\Tests;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\DefaultNode;
use Thinktomorrow\Vine\NodeCollection;

class DefaultNodeTest extends TestCase
{
    /** @test */
    public function it_can_add_an_entry_value_to_a_node()
    {
        $node = new DefaultNode(['foobar' => 'baz']);
        $this->assertEquals('baz', $node->getNodeValue('foobar'));

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
    public function it_can_get_parent_id()
    {
        $node = new DefaultNode('foobar');

        $this->assertNull($node->getParentNodeId());

        $node->addChildNodes([
            $child = new DefaultNode('first-child'),
        ]);

        $this->assertEquals($node->getNodeId(), $child->getParentNodeId());
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
    public function it_can_fetch_entry_values_via_node()
    {
        $node = new DefaultNode(['id' => 1, 'label' => 'foobar']);

        $this->assertEquals(1, $node->getNodeValue('id'));
        $this->assertEquals('foobar', $node->getNodeValue('label'));
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

    public function test_it_can_get_siblings()
    {
        $node = new DefaultNode(1);
        $node->addChildNodes([$child = new DefaultNode(2)]);
        $node->addChildNodes([$child2 = new DefaultNode(3)]);

        $this->assertCount(0, $node->getSiblingNodes());
        $this->assertCount(1, $child->getSiblingNodes());
        $this->assertCount(1, $child2->getSiblingNodes());
        $this->assertEquals($child2, $child->getSiblingNodes()->first());
        $this->assertEquals($child, $child2->getSiblingNodes()->first());
    }

    public function test_siblings_of_root_do_not_exist()
    {
        $nodes = new NodeCollection([
            $root = new DefaultNode(1),
            $root2 = new DefaultNode(2),
        ]);

        $this->assertCount(2, $nodes);
        $this->assertCount(0, $root->getSiblingNodes());
        $this->assertCount(0, $root2->getSiblingNodes());
    }

    /** @test */
    public function it_can_check_if_it_has_siblings()
    {
        $node = new DefaultNode(1);
        $node->addChildNodes([$child = new DefaultNode(2)]);
        $node->addChildNodes([$child2 = new DefaultNode(3)]);

        $this->assertFalse($node->hasSiblingNodes());
        $this->assertTrue($child->hasSiblingNodes());
        $this->assertTrue($child2->hasSiblingNodes());
    }

    public function test_it_can_get_left_and_right_sibling()
    {
        $node = new DefaultNode(1);
        $node->addChildNodes([$child = new DefaultNode(2)]);
        $node->addChildNodes([$child2 = new DefaultNode(3)]);

        $this->assertNull($child->getLeftSiblingNode());
        $this->assertEquals($child2, $child->getRightSiblingNode());

        $this->assertEquals($child, $child2->getLeftSiblingNode());
        $this->assertNull($child2->getRightSiblingNode());
    }

    public function test_root_has_no_siblings()
    {
        $nodes = new NodeCollection([
            $root = new DefaultNode(1),
            new DefaultNode(2),
        ]);

        $this->assertNull($root->getLeftSiblingNode());
        $this->assertNull($root->getRightSiblingNode());
    }

    public function test_it_can_transform_to_array()
    {
        $node = new DefaultNode((object)['id' => 2, 'parent_id' => 5, 'foo' => 'bar']);
        $this->assertEquals([
            'id' => '2',
            'parent_id' => '5',
            'entry' => (object)['id' => 2, 'parent_id' => 5, 'foo' => 'bar'],
            'children' => [],
        ], $node->toArray());
    }

    public function test_it_can_transform_incomplete_entry_to_array()
    {
        $node = new DefaultNode(2);
        $this->assertEquals([
            'id' => '',
            'parent_id' => null,
            'entry' => 2,
            'children' => [],
        ], $node->toArray());
    }
}
