<?php

namespace Thinktomorrow\Vine\Tests\Commands;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\DefaultNode;

class FlattenTest extends TestCase
{
    public function test_it_can_flatten_a_node_collection()
    {
        $node = new DefaultNode(['id' => 1, 'name' => 'foobar']);
        $node->addChildNodes([$child = new DefaultNode(['id' => 2, 'name' => 'first-child'])]);
        $child->addChildNodes([$child2 = new DefaultNode(['id' => 3, 'name' => 'second-child'])]);

        $flatNodes = (new \Thinktomorrow\Vine\NodeCollection([$node]))->flatten();

        $this->assertEquals(3, $flatNodes->count());
        $this->assertSame($node, $flatNodes[0]);
        $this->assertSame($child, $flatNodes[1]);
        $this->assertSame($child2, $flatNodes[2]);
    }
}
