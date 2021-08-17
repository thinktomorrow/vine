<?php

use Vine\Node;

class MoveTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function a_node_can_be_moved_to_different_parent()
    {
        $node = new Node(['id' => 1, 'name' => 'root-1']);
        $node2 = new Node(['id' => 2, 'name' => 'root-2']);
        $node->addChildren([$child = new Node(['id' => 3, 'name' => 'first-child'])]);

        // Assert defaults
        $this->assertCount(1, $node->getChildren());
        $this->assertEmpty($node2->getChildren());

        $child->move($node2);

        // Assert move
        $this->assertEmpty($node->getChildren());
        $this->assertCount(1, $node2->getChildren());
        $this->assertSame($child, $node2->getChildren()->first());
        $this->assertSame($node2, $child->parent());
    }

    /** @test */
    public function a_node_is_moved_along_with_its_children()
    {
        $root = new Node(['id' => 1, 'name' => 'root-1']);
        $root->addChildren([$main1 = new Node(['id' => 2, 'name' => 'child-1'])]);
        $main1->addChildren([$child3 = new Node(['id' => 4, 'name' => 'child-3'])]);
        $child3->addChildren([$child4 = new Node(['id' => 5, 'name' => 'child-4'])]);
        $root->addChildren([$main2 = new Node(['id' => 3, 'name' => 'child-2'])]);
        $main2->addChildren([$child5 = new Node(['id' => 6, 'name' => 'child-5'])]);

        // Assert defaults
        $this->assertSame($main1, $child3->parent());

        $child3->move($main2);

        // Assert move
        $this->assertEmpty($main1->getChildren());
        $this->assertCount(2, $main2->getChildren());
        $this->assertSame($main2, $child3->parent());
        $this->assertSame($child4, $child3->getChildren()->first());
    }

    /** @test */
    public function a_node_can_be_moved_to_root()
    {
        $root = new Node(['id' => 1, 'name' => 'root-1']);
        $root->addChildren([$main1 = new Node(['id' => 2, 'name' => 'child-1'])]);
        $main1->addChildren([$child1 = new Node(['id' => 4, 'name' => 'child-3'])]);

        $child1->moveToRoot();

        // Assert move
        $this->assertNull($child1->parent());
        $this->assertTrue($child1->isRoot());
        $this->assertCount(0, $main1->getChildren());
    }
}
