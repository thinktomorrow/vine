<?php

use PHPUnit\Framework\TestCase;
use Vine\Node;

class FlattenTest extends TestCase
{
    /** @test */
    public function it_can_flatten_a_node_collection()
    {
        $node = new Node(['id' => 1, 'name' => 'foobar']);
        $node->addChildren([$child = new Node(['id' => 2, 'name' => 'first-child'])]);
        $child->addChildren([$child2 = new Node(['id' => 3, 'name' => 'second-child'])]);

        $flatNodes = (new \Vine\NodeCollection($node))->flatten();

        $this->assertEquals(3, $flatNodes->count());
        $this->assertSame($node, $flatNodes[0]);
        $this->assertSame($child, $flatNodes[1]);
        $this->assertSame($child2, $flatNodes[2]);
    }
}
