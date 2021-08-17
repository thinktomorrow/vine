<?php

use PHPUnit\Framework\TestCase;
use Vine\Node;
use Vine\NodeCollection;

class FindTest extends TestCase
{
    /** @test */
    public function it_can_find_many_nodes_by_their_primary_identifiers()
    {
        $node = new Node(['id' => 1]);
        $node->addChildren([$child = new Node(['id' => 2])]);
        $child->addChildren([$child2 = new Node(['id' => 3])]);

        // Not part of the result set
        $child->addChildren([$child3 = new Node(['id' => 4])]);
        $node->addChildren([$child4 = new Node(['id' => 5])]);

        $nodes = $node->getChildren()->findMany('id', [2, 3]);

        $this->assertInstanceOf(NodeCollection::class, $nodes);
        $this->assertCount(2, $nodes);
        $this->assertSame($child, $nodes[0]);
        $this->assertSame($child2, $nodes[1]);

        // Node delegates the same method to child collection
        $this->assertEquals($nodes, $node->findMany('id', [2, 3]));
    }

    /** @test */
    public function it_can_find_a_node_by_its_primary_identifier()
    {
        $node = new Node(['id' => 1]);
        $node->addChildren([$child = new Node(['id' => 2])]);
        $child->addChildren([$child2 = new Node(['id' => 3])]);

        $result = $node->getChildren()->find('id', 2);

        $this->assertInstanceOf(Node::class, $result);
        $this->assertSame($child, $result);

        // Node delegates the same method to child collection
        $this->assertSame($result, $node->find('id', 2));
    }

    //    /** @test */
    //    function it_can_find_a_node_with_a_value_as_entry()
    //    {
    //        $node = new Node('foobar');
    //        $node->addChildren([$child = new Node('fooberry')]);
    //        $child->addChildren([$child2 = new Node('foobarry')]);
    //
    //        $result = $node->getChildren()->find('foobarry');
    //
    //        $this->assertInstanceOf(Node::class, $result);
    //        $this->assertSame($child2, $result);
    //
    //        // Node delegates the same method to child collection
    //        $this->assertSame($result,$node->find('foobarry'));
    //    }
}
