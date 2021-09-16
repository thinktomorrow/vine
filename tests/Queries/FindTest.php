<?php

namespace Thinktomorrow\Vine\Tests\Queries;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\DefaultNode;
use Thinktomorrow\Vine\NodeCollection;

class FindTest extends TestCase
{
    /** @test */
    public function it_can_find_many_nodes_by_their_primary_identifiers()
    {
        $node = new DefaultNode(['id' => 1]);
        $node->addChildNodes([$child = new DefaultNode(['id' => 2])]);
        $child->addChildNodes([$child2 = new DefaultNode(['id' => 3])]);

        // Not part of the result set
        $child->addChildNodes([$child3 = new DefaultNode(['id' => 4])]);
        $node->addChildNodes([$child4 = new DefaultNode(['id' => 5])]);

        $nodes = $node->getChildNodes()->findMany('id', [2, 3]);

        $this->assertInstanceOf(NodeCollection::class, $nodes);
        $this->assertCount(2, $nodes);
        $this->assertSame($child, $nodes[0]);
        $this->assertSame($child2, $nodes[1]);

        // Node delegates the same method to child collection
        $this->assertEquals($nodes, $node->findChildNodes('id', [2, 3]));
    }

    /** @test */
    public function it_can_find_many_nodes_by_callback()
    {
        $node = new DefaultNode(['id' => 1]);
        $node->addChildNodes([$child = new DefaultNode(['id' => 2])]);
        $child->addChildNodes([$child2 = new DefaultNode(['id' => 3])]);

        $nodes = $node->getChildNodes()->findMany(function ($node) {
            return in_array($node->getNodeId(), [2,3]);
        });

        $this->assertInstanceOf(NodeCollection::class, $nodes);
        $this->assertCount(2, $nodes);
        $this->assertSame($child, $nodes[0]);
        $this->assertSame($child2, $nodes[1]);

        // Node delegates the same method to child collection
        $this->assertEquals($nodes, $node->findChildNodes('id', [2, 3]));
    }

    /** @test */
    public function it_can_find_a_node_by_its_primary_identifier()
    {
        $node = new DefaultNode(['id' => 1]);
        $node->addChildNodes([$child = new DefaultNode(['id' => 2])]);
        $child->addChildNodes([$child2 = new DefaultNode(['id' => 3])]);

        $result = $node->getChildNodes()->find('id', 2);

        $this->assertInstanceOf(DefaultNode::class, $result);
        $this->assertSame($child, $result);

        // Node delegates the same method to child collection
        $this->assertSame($result, $node->findChildNode('id', 2));
    }
}
