<?php

namespace Thinktomorrow\Vine\Tests;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\NodeCollection;
use Thinktomorrow\Vine\Tests\Fixtures\CustomModelNode;
use Thinktomorrow\Vine\Tests\Fixtures\CustomNodeCollection;

class NodeTest extends TestCase
{
    public function test_it_can_add_an_entry_value_to_a_node()
    {
        $node = new CustomModelNode(1, null, ['foobar' => 'baz']);
        $this->assertEquals('baz', $node->getNodeValue('foobar'));

        $node = new CustomModelNode(1, null, $entry = ['foo' => 'bar']);
        $this->assertSame($entry, $node->getValues());
    }

    public function test_it_can_add_children_to_a_node()
    {
        $node = new CustomModelNode(1);
        $this->assertEmpty($node->getChildNodes());

        $this->assertCount(2, $node->addChildNodes([
            new CustomModelNode(2),
            new CustomModelNode(3),
        ])->getChildNodes());
    }

    public function test_it_can_get_parent_id()
    {
        $node = new CustomModelNode(1);

        $this->assertNull($node->getParentNodeId());

        $node->addChildNodes([
            $child = new CustomModelNode(2),
        ]);

        $this->assertEquals($node->getNodeId(), $child->getParentNodeId());
    }

    public function test_an_added_child_must_be_node_or_collection()
    {
        $this->expectException(\InvalidArgumentException::class);

        $node = new CustomModelNode(1);
        $node->addChildNodes('string is not allowed');
    }

    public function test_it_can_add_children_to_a_node_consecutively()
    {
        $node = new CustomModelNode(1);
        $this->assertEmpty($node->getChildNodes());

        $node->addChildNodes([new CustomModelNode(2)]);

        $this->assertCount(2, $node->addChildNodes([
            new CustomModelNode(3),
        ])->getChildNodes());
    }

    public function test_by_setting_children_the_parent_is_set_for_each_one()
    {
        $node = new CustomModelNode(1);
        $node->addChildNodes([$child = new CustomModelNode(2)]);

        $this->assertSame($node, $child->getParentNode());
    }

    public function test_it_can_verify_a_node_is_equal()
    {
        $first = new CustomModelNode(1);
        $second = $first;

        $this->assertTrue($first->equalsNode($second));
    }

    public function test_a_node_is_a_leaf_if_it_has_no_children()
    {
        $node = new CustomModelNode(1);
        $node->addChildNodes([$child = new CustomModelNode(2)]);

        $this->assertFalse($node->isLeafNode());
        $this->assertTrue($child->isLeafNode());
    }

    public function test_a_node_is_a_root_if_it_has_no_parent()
    {
        $node = new CustomModelNode(1);
        $node->addChildNodes([$child = new CustomModelNode(2)]);

        $this->assertTrue($node->isRootNode());
        $this->assertFalse($child->isRootNode());
    }

    public function test_it_can_get_depth()
    {
        $node = new CustomModelNode(1);
        $node->addChildNodes([$child = new CustomModelNode(2)]);
        $child->addChildNodes([$child2 = new CustomModelNode(3)]);

        $this->assertEquals(2, $child2->getNodeDepth());
        $this->assertEquals(1, $child->getNodeDepth());
        $this->assertEquals(0, $node->getNodeDepth());
    }

    public function test_it_can_get_total_count_of_all_children()
    {
        $node = new CustomModelNode(1);
        $node->addChildNodes([$child = new CustomModelNode(2)]);
        $child->addChildNodes([$child2 = new CustomModelNode(3)]);

        $this->assertEquals(0, $child2->getTotalChildNodesCount());
        $this->assertEquals(1, $child->getTotalChildNodesCount());
        $this->assertEquals(2, $node->getTotalChildNodesCount());
    }

    public function test_it_can_remove_self_from_parent()
    {
        $node = new CustomModelNode(1);
        $node->addChildNodes([$child = new CustomModelNode(2)]);
        $child->addChildNodes([$child2 = new CustomModelNode(3)]);

        $child->getParentNode()->removeNode($child);

        $this->assertCount(0, $node->getChildNodes());
        $this->assertCount(1, $child->getChildNodes());
        $this->assertNull($child->getParentNode());

        // Assert parent still exists
        $this->assertInstanceOf(CustomModelNode::class, $node);
    }

    public function test_it_can_fetch_entry_values_via_node()
    {
        $node = new CustomModelNode(1, null, ['id' => 1, 'label' => 'foobar']);

        $this->assertEquals(1, $node->getNodeValue('id'));
        $this->assertEquals('foobar', $node->getNodeValue('label'));
    }

    public function test_it_can_fetch_children_as_property_call()
    {
        $node = new CustomModelNode(1);
        $node->addChildNodes([$child = new CustomModelNode(2)]);

        $this->assertInstanceOf(NodeCollection::class, $node->getChildNodes());
        $this->assertSame($child, $node->getChildNodes()->first());
    }

    public function test_it_can_check_if_it_has_children()
    {
        $node = new CustomModelNode(1);
        $node->addChildNodes([$child = new CustomModelNode(2)]);
        $child->addChildNodes([$child2 = new CustomModelNode(3)]);

        $this->assertTrue($node->hasChildNodes());
        $this->assertFalse($child2->hasChildNodes());
    }

    public function test_it_can_get_siblings()
    {
        $node = new CustomModelNode(1);
        $node->addChildNodes([$child = new CustomModelNode(2)]);
        $node->addChildNodes([$child2 = new CustomModelNode(3)]);

        $this->assertCount(0, $node->getSiblingNodes());
        $this->assertCount(1, $child->getSiblingNodes());
        $this->assertCount(1, $child2->getSiblingNodes());
        $this->assertEquals($child2, $child->getSiblingNodes()->first());
        $this->assertEquals($child, $child2->getSiblingNodes()->first());
    }

    public function test_siblings_of_root_do_not_exist()
    {
        $nodes = new NodeCollection([
            $root = new CustomModelNode(1),
            $root2 = new CustomModelNode(2),
        ]);

        $this->assertCount(2, $nodes);
        $this->assertCount(0, $root->getSiblingNodes());
        $this->assertCount(0, $root2->getSiblingNodes());
    }

    public function test_it_can_check_if_it_has_siblings()
    {
        $node = new CustomModelNode(1);
        $node->addChildNodes([$child = new CustomModelNode(2)]);
        $node->addChildNodes([$child2 = new CustomModelNode(3)]);

        $this->assertFalse($node->hasSiblingNodes());
        $this->assertTrue($child->hasSiblingNodes());
        $this->assertTrue($child2->hasSiblingNodes());
    }

    public function test_it_can_get_left_and_right_sibling()
    {
        $node = new CustomModelNode(1);
        $node->addChildNodes([$child = new CustomModelNode(2)]);
        $node->addChildNodes([$child2 = new CustomModelNode(3)]);

        $this->assertNull($child->getLeftSiblingNode());
        $this->assertEquals($child2, $child->getRightSiblingNode());

        $this->assertEquals($child, $child2->getLeftSiblingNode());
        $this->assertNull($child2->getRightSiblingNode());
    }

    public function test_root_has_no_siblings()
    {
        $nodes = new NodeCollection([
            $root = new CustomModelNode(1),
            new CustomModelNode(2),
        ]);

        $this->assertNull($root->getLeftSiblingNode());
        $this->assertNull($root->getRightSiblingNode());
    }

    public function test_it_can_transform_to_array()
    {
        $node = new CustomModelNode(2, 5, ['id' => 2, 'parent_id' => 5, 'foo' => 'bar']);
        $this->assertEquals([
            'id' => '2',
            'parent_id' => '5',
            'values' => ['id' => 2, 'parent_id' => 5, 'foo' => 'bar'],
            'children' => [],
        ], $node->toArray());
    }

    public function test_it_can_transform_incomplete_entry_to_array()
    {
        $node = new CustomModelNode(2);
        $this->assertEquals([
            'id' => 2,
            'parent_id' => null,
            'values' => [],
            'children' => [],
        ], $node->toArray());
    }

    public function test_model_can_be_node()
    {
        $collection = CustomNodeCollection::fromIterable([
            $model1 = new CustomModelNode('1', null, ['name' => 'foobar']),
            $model2 = new CustomModelNode('2', '1', ['name' => 'foobar-2']),
            $model3 = new CustomModelNode('3', '2', ['name' => 'foobar-3']),
        ]);

        $this->assertCount(1, $collection->all());
        $this->assertEquals(3, $collection->total());

        $this->assertEquals($model1, $collection->findById('1'));
        $this->assertEquals($model2, $collection->findById('2'));
        $this->assertEquals($model3, $collection->findById('3'));

        $this->assertEquals($model1, $model2->getParentNode());
        $this->assertEquals($model3, $model2->getChildNodes()->first());
    }

    public function test_model_can_be_referenced_via_object_memory()
    {
        $collection = CustomNodeCollection::fromIterable([
            $model1 = new CustomModelNode('1', null, ['name' => 'foobar']),
            $model2 = new CustomModelNode('2', '1', ['name' => 'foobar-2']),
            $model3 = new CustomModelNode('3', '2', ['name' => 'foobar-3']),
        ]);

        $this->assertEquals($model1, $model2->getParentNode());
        $this->assertEquals($model3, $model2->getChildNodes()->first());
    }
}
