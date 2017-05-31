<?php

use PHPUnit\Framework\TestCase;
use Vine\Node;

class ShakeTest extends TestCase
{
    /** @test */
    function a_node_collection_that_does_not_need_shaking_is_copied_but_has_exact_same_structure()
    {
        $node = $this->getNode();

        $shakedNode = $node->shake(function(Node $node){
            return true;
        });

        // Original is preserved
        $this->assertEquals(1, $node->children()->count());
        $this->assertEquals(2, $node->children()->first()->children()->count());

        $this->assertEquals($node, $shakedNode);
        $this->assertNotSame($node->children(), $shakedNode->children());
        $this->assertEquals($node->children(), $shakedNode->children());
        $this->assertEquals(3, $shakedNode->total());
        $this->assertEquals(1, $shakedNode->count());
    }

    /** @test */
    function if_all_is_shaken_only_the_root_remains()
    {
        $node = $this->getNode();

        $shakedNode = $node->shake(function(Node $node){
            return false;
        });

        $this->assertEquals($node->isolatedCopy(), $shakedNode);
    }

    /** @test */
    function shake_maintains_the_ancestors_for_each_kept_node()
    {
        $node = $this->getNode();

        $shakedNode = $node->shake(function(Node $node){
            return $node->id == 3;
        });

        $this->assertEquals(
            (new Node(['id' => 1, 'name' => 'foobar']))
                ->addChildren(
                    (new Node(['id' => 2, 'name' => 'first-child']))
                        ->addChildren(new Node(['id' => 3, 'name' => 'second-child']))
                ),
            $shakedNode
        );
    }

    /** @test */
    function it_can_shake_a_node_collection()
    {
        $nodeCollection = $this->getNode()->children();

        $shakedNodeCollection = $nodeCollection->shake(function(Node $node){
            return $node->id == 3;
        });

        $this->assertEquals(
            new \Vine\NodeCollection((new Node(['id' => 2, 'name' => 'first-child']))
                ->addChildren(new Node(['id' => 3, 'name' => 'second-child']))
            ),
            $shakedNodeCollection
        );
    }

    /**
     * @return Node
     */
    private function getNode()
    {
        $node = new Node(['id' => 1, 'name' => 'foobar']);
        $node->addChildren([$child = new Node(['id' => 2, 'name' => 'first-child'])]);
        $child->addChildren([$child2 = new Node(['id' => 3, 'name' => 'second-child'])]);
        $child->addChildren([$child3 = new Node(['id' => 4, 'name' => 'third-child'])]);

        return $node;
    }

}