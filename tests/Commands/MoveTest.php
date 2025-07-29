<?php

namespace Thinktomorrow\Vine\Tests\Commands;

use Thinktomorrow\Vine\DefaultNode;

class MoveTest extends \PHPUnit\Framework\TestCase
{
    public function test_a_node_can_be_moved_to_different_parent()
    {
        $node = new DefaultNode(['id' => 1, 'name' => 'root-1']);
        $node2 = new DefaultNode(['id' => 2, 'name' => 'root-2']);
        $node->addChildNodes([$child = new DefaultNode(['id' => 3, 'name' => 'first-child'])]);

        // Assert defaults
        $this->assertCount(1, $node->getChildNodes());
        $this->assertEmpty($node2->getChildNodes());

        $child->moveToParentNode($node2);

        // Assert move
        $this->assertEmpty($node->getChildNodes());
        $this->assertCount(1, $node2->getChildNodes());
        $this->assertSame($child, $node2->getChildNodes()->first());
        $this->assertSame($node2, $child->getParentNode());
    }

    public function test_a_node_is_moved_along_with_its_children()
    {
        $root = new DefaultNode(['id' => 1, 'name' => 'root-1']);
        $root->addChildNodes([$main1 = new DefaultNode(['id' => 2, 'name' => 'child-1'])]);
        $main1->addChildNodes([$child3 = new DefaultNode(['id' => 4, 'name' => 'child-3'])]);
        $child3->addChildNodes([$child4 = new DefaultNode(['id' => 5, 'name' => 'child-4'])]);
        $root->addChildNodes([$main2 = new DefaultNode(['id' => 3, 'name' => 'child-2'])]);
        $main2->addChildNodes([$child5 = new DefaultNode(['id' => 6, 'name' => 'child-5'])]);

        // Assert defaults
        $this->assertSame($main1, $child3->getParentNode());

        $child3->moveToParentNode($main2);

        // Assert move
        $this->assertEmpty($main1->getChildNodes());
        $this->assertCount(2, $main2->getChildNodes());
        $this->assertSame($main2, $child3->getParentNode());
        $this->assertSame($child4, $child3->getChildNodes()->first());
    }

    public function test_a_node_can_be_moved_to_root()
    {
        $root = new DefaultNode(['id' => 1, 'name' => 'root-1']);
        $root->addChildNodes([$main1 = new DefaultNode(['id' => 2, 'name' => 'child-1'])]);
        $main1->addChildNodes([$child1 = new DefaultNode(['id' => 4, 'name' => 'child-3'])]);

        $child1->moveNodeToRoot();

        // Assert move
        $this->assertNull($child1->getParentNode());
        $this->assertTrue($child1->isRootNode());
        $this->assertCount(0, $main1->getChildNodes());
    }
}
