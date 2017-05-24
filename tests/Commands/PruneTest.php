<?php

use PHPUnit\Framework\TestCase;
use Vine\Node;

class PruneTest extends TestCase
{
    /** @test */
    function a_node_collection_that_does_not_need_pruning_is_copied_but_has_exact_same_structure()
    {
        $node = $this->getNode();

        $prunedNode = $node->prune(function(Node $node){
            return true;
        });

        // Original is preserved
        $this->assertEquals(1, $node->children()->count());
        $this->assertEquals(2, $node->children()->first()->children()->count());

        $this->assertEquals($node, $prunedNode);
        $this->assertNotSame($node->children(), $prunedNode->children());
        $this->assertEquals($node->children(), $prunedNode->children());
        $this->assertEquals(3, $prunedNode->total());
        $this->assertEquals(1, $prunedNode->count());
    }

    /** @test */
    function if_all_is_pruned_only_the_root_remains()
    {
        $node = $this->getNode();

        $prunedNode = $node->prune(function(Node $node){
            return false;
        });

        $this->assertEquals($node->isolatedCopy(), $prunedNode);
    }

    /** @test */
    function it_can_prune_by_specific_closure()
    {
        $node = $this->getNode();

        $prunedNode = $node->prune(function(Node $node){
            return $node->id == 3;
        });

        $this->assertEquals(
            (new Node(['id' => 1, 'name' => 'foobar']))->addChildren(new Node(['id' => 3, 'name' => 'second-child'])),
            $prunedNode
        );
    }

    /** @test */
    function prune_maintains_the_ancestors_for_each_kept_node()
    {
        $node = $this->getNode();

        $prunedNode = $node->prune(function(Node $node){
            return $node->id == 3;
        });

        $this->assertEquals(
            (new Node(['id' => 1, 'name' => 'foobar']))->addChildren(new Node(['id' => 3, 'name' => 'second-child'])),
            $prunedNode
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