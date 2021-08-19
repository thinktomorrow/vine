<?php

namespace Thinktomorrow\Vine\Tests\Commands;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\DefaultNode;

class InflateTest extends TestCase
{
    /** @test */
    public function it_can_inflate_a_flattened_node_collection_back_to_its_original_structure()
    {
        $node = new DefaultNode(['id' => 1, 'name' => 'foobar']);
        $node->addChildNodes([$child = new DefaultNode(['id' => 2, 'name' => 'first-child'])]);
        $child->addChildNodes([$child2 = new DefaultNode(['id' => 3, 'name' => 'second-child'])]);

        $flatNodes = (new \Thinktomorrow\Vine\NodeCollection($node))->flatten();
        $this->assertEquals(3, $flatNodes->count());

        $inflatedNodes = $flatNodes->inflate();

        $this->assertEquals(1, $inflatedNodes->count());
        $this->assertSame($node, $inflatedNodes->first());
    }

    /** @test */
    public function inflating_a_non_flattened_collection_remains_the_same()
    {
        $node = new DefaultNode(['id' => 1, 'name' => 'foobar']);
        $node->addChildNodes([$child = new DefaultNode(['id' => 2, 'name' => 'first-child'])]);
        $child->addChildNodes([$child2 = new DefaultNode(['id' => 3, 'name' => 'second-child'])]);

        $collection = new \Thinktomorrow\Vine\NodeCollection($node);

        $this->assertEquals(1, $collection->inflate()->count());
        $this->assertSame($node, $collection->inflate()->first());
    }
}
