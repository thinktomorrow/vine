<?php

use PHPUnit\Framework\TestCase;
use Vine\Node;
use Vine\NodeCollection;

class NodeCollectionTest extends TestCase
{
    /** @test */
    function it_contains_array_of_nodes()
    {
        $collection = new \Vine\NodeCollection();

        $this->assertInternalType('array', $collection->all());
        $this->assertCount(0, $collection->all());
    }

    /** @test */
    function it_accepts_variadic_array_of_nodes()
    {
        $collection = new \Vine\NodeCollection(
            new Node('foobar'),
            new Node('foobar-2')
        );

        $this->assertCount(2, $collection->all());
    }

    /** @test */
    function it_can_add_array_of_nodes()
    {
        $collection = new \Vine\NodeCollection(
            new Node('foobar')
        );

        $collection->add(
            new Node('foobar-2'),
            new Node('foobar-3')
        );

        $this->assertCount(3, $collection->all());
    }

    /** @test */
    function it_can_find_many_nodes_by_their_primary_identifiers()
    {
        $node = new Node(['id' => 1]);
        $node->addChildren([$child = new Node(['id' => 2])]);
        $child->addChildren([$child2 = new Node(['id' => 3])]);

        // Not part of the result set
        $child->addChildren([$child3 = new Node(['id' => 4])]);
        $node->addChildren([$child4 = new Node(['id' => 5])]);

        $nodes = $node->children()->findMany('id',[2,3]);

        $this->assertInstanceOf(NodeCollection::class, $nodes);
        $this->assertCount(2,$nodes);
        $this->assertSame($child, $nodes[0]);
        $this->assertSame($child2, $nodes[1]);

        // Node delegates the same method to child collection
        $this->assertEquals($nodes,$node->findMany('id',[2,3]));

    }

    /** @test */
    function it_can_find_a_node_by_its_primary_identifier()
    {
        $node = new Node(['id' => 1]);
        $node->addChildren([$child = new Node(['id' => 2])]);
        $child->addChildren([$child2 = new Node(['id' => 3])]);

        $result = $node->children()->find('id',2);

        $this->assertInstanceOf(Node::class, $result);
        $this->assertSame($child, $result);

        // Node delegates the same method to child collection
        $this->assertSame($result,$node->find('id',2));
    }

    //    /** @test */
    //    function it_can_find_a_node_with_a_value_as_entry()
    //    {
    //        $node = new Node('foobar');
    //        $node->addChildren([$child = new Node('fooberry')]);
    //        $child->addChildren([$child2 = new Node('foobarry')]);
    //
    //        $result = $node->children()->find('foobarry');
    //
    //        $this->assertInstanceOf(Node::class, $result);
    //        $this->assertSame($child2, $result);
    //
    //        // Node delegates the same method to child collection
    //        $this->assertSame($result,$node->find('foobarry'));
    //    }

    /** @test */
    function it_can_remove_nodes()
    {
        $collection = new \Vine\NodeCollection(
            $child = new Node(['id' => 1, 'name' => 'foobar']),
            $child2 = new Node(['id' => 2, 'name' => 'foobar-2']),
            $child3 = new Node(['id' => 3, 'name' => 'foobar-3'])
        );

        $collection->remove($child);

        $this->assertCount(2, $collection->all());
        $this->assertSame($child2,$collection->find('id',2));
        $this->assertSame($child3,$collection->find('id',3));
        $this->assertNull($collection->find('id',1));
    }

    /** @test */
    function it_can_remove_nested_nodes()
    {
        $root = new Node(['id' => 1, 'name' => 'foobar']);
        $root->addChildren($child2 = new Node(['id' => 2, 'name' => 'foobar-2']));
        $child2->addChildren($child4 = new Node(['id' => 4, 'name' => 'foobar-4']));

        $child2->addChildren($child3 = new Node(['id' => 3, 'name' => 'foobar-3']));
        $child3->addChildren($child5 = new Node(['id' => 5, 'name' => 'foobar-5']));

        $root->remove($child3);

        $this->assertEquals(2, $root->total());
        $this->assertSame($child2,$root->children()->find('id',2));
        $this->assertSame($child4,$root->children()->find('id',4));
        $this->assertNull($root->children()->find('id',3));
        $this->assertNull($root->children()->find('id',5));
    }
}