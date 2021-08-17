<?php

use Vine\Node;

class SliceTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function when_removing_node_from_flat_collection_node_is_removed_and_children_are_added_to_ancestor()
    {
        $node = new Node(['id' => 1, 'name' => 'foobar']);
        $node->addChildren([$child = new Node(['id' => 2, 'name' => 'first-child'])]);
        $child->addChildren([$child2 = new Node(['id' => 3, 'name' => 'second-child'])]);

        $collection = (new \Vine\NodeCollection($node))->slice($child);

        // Child2 is now direct child of node
        $newChild2 = $collection->first()->getChildren()->first();

        $this->assertEquals(2, $collection->total());
        $this->assertSame($node, $collection->first());
        $this->assertSame($child2, $newChild2);
        $this->assertSame($node, $newChild2->parent());

        $this->assertEquals(1, $node->total());
        $this->assertEmpty($newChild2->getChildren());
    }
}
