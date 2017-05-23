<?php

use PHPUnit\Framework\TestCase;
use Vine\Node;

class FilterTest extends TestCase
{
    /** @test */
    function it_can_filter_by_closure()
    {
        $node = new Node(['id' => 1, 'name' => 'foobar']);
        $node->addChildren([$child = new Node(['id' => 2, 'name' => 'first-child'])]);
        $child->addChildren([$child2 = new Node(['id' => 3, 'name' => 'second-child'])]);
        $child->addChildren([$child3 = new Node(['id' => 4, 'name' => 'third-child'])]);

        $filteredNodes = $node->children()->filter(function(Node $node){
            return true;
        });
var_dump($filteredNodes);
die();
        $this->assertSame($node->children(), $filteredNodes);
        $this->assertEquals(2, $filteredNodes->count());
    }
//
//    /** @test */
//    function filter_maintains_the_tree_structure()
//    {
//        $node = new Node(['id' => 1, 'name' => 'foobar']);
//        $node->addChildren([$child = new Node(['id' => 2, 'name' => 'first-child'])]);
//        $child->addChildren([$child2 = new Node(['id' => 3, 'name' => 'second-child'])]);
//
//        $filteredNode = $node->filter(function(Node $node){
//            return true;
//        });
//
//        $this->assertEquals([
//            1,2,3
//        ],$node->pluck('id'));
//    }

}