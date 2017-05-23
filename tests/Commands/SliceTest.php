<?php

use Vine\Node;

class SliceTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    function when_removing_node_from_flat_collection_node_is_removed_and_children_are_added_to_ancestor()
    {
        $node = new Node(['id' => 1, 'name' => 'foobar']);
        $node->addChildren([$child = new Node(['id' => 2, 'name' => 'first-child'])]);
        $child->addChildren([$child2 = new Node(['id' => 3, 'name' => 'second-child'])]);

        $collection = (new \Vine\NodeCollection($node))->slice($child);

        // Child2 is now direct child of node
        $newChild2 = $collection->first()->children()->first();

        $this->assertEquals(1, $collection->count());
        $this->assertNotSame($node, $collection->first());
        $this->assertNotSame($child2, $newChild2);
        $this->assertSame($node, $newChild2->parent());

        $this->assertCount(1,$node->children());
        $this->assertEmpty($newChild2->children());
    }
}