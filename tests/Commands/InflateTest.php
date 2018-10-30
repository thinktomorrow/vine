<?php

use PHPUnit\Framework\TestCase;
use Vine\Node;

class InflateTest extends TestCase
{
    /** @test */
    public function it_can_inflate_a_flattened_node_collection_back_to_its_original_structure()
    {
        $node = new Node(['id' => 1, 'name' => 'foobar']);
        $node->addChildren([$child = new Node(['id' => 2, 'name' => 'first-child'])]);
        $child->addChildren([$child2 = new Node(['id' => 3, 'name' => 'second-child'])]);

        $flatNodes = (new \Vine\NodeCollection($node))->flatten();
        $this->assertEquals(3, $flatNodes->count());

        $inflatedNodes = $flatNodes->inflate();

        $this->assertEquals(1, $inflatedNodes->count());
        $this->assertSame($node, $inflatedNodes->first());
    }

    /** @test */
    public function inflating_a_non_flattened_collection_remains_the_same()
    {
        $node = new Node(['id' => 1, 'name' => 'foobar']);
        $node->addChildren([$child = new Node(['id' => 2, 'name' => 'first-child'])]);
        $child->addChildren([$child2 = new Node(['id' => 3, 'name' => 'second-child'])]);

        $collection = new \Vine\NodeCollection($node);

        $this->assertEquals(1, $collection->inflate()->count());
        $this->assertSame($node, $collection->inflate()->first());
    }
}
