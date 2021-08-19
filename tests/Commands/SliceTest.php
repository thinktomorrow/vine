<?php

namespace Thinktomorrow\Vine\Tests\Commands;

use Thinktomorrow\Vine\NodeCollection;
use Thinktomorrow\Vine\DefaultNode;

class SliceTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function when_removing_node_from_flat_collection_node_is_removed_and_children_are_added_to_ancestor()
    {
        $node = new DefaultNode(['id' => 1, 'name' => 'foobar']);
        $node->addChildNodes([$child = new DefaultNode(['id' => 2, 'name' => 'first-child'])]);
        $child->addChildNodes([$child2 = new DefaultNode(['id' => 3, 'name' => 'second-child'])]);

        $collection = (new NodeCollection($node))->slice($child);

        // Child2 is now direct child of node
        $newChild2 = $collection->first()->getChildNodes()->first();

        $this->assertEquals(2, $collection->total());
        $this->assertSame($node, $collection->first());
        $this->assertSame($child2, $newChild2);
        $this->assertSame($node, $newChild2->getParentNode());

        $this->assertEquals(1, $node->getTotalChildNodesCount());
        $this->assertEmpty($newChild2->getChildNodes());
    }
}
