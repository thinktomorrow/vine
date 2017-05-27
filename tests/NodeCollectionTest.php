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