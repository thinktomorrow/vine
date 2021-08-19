<?php

namespace Thinktomorrow\Vine\Tests\Commands;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\DefaultNode;
use Thinktomorrow\Vine\Node;

class PruneTest extends TestCase
{
    /** @test */
    public function a_node_collection_that_does_not_need_pruning_is_copied_but_has_exact_same_structure()
    {
        $node = $this->getNode();

        $prunedNode = $node->pruneChildNodes(function (Node $node) {
            return true;
        });

        // Original is preserved
        $this->assertEquals(1, $node->getChildNodes()->count());
        $this->assertEquals(2, $node->getChildNodes()->first()->getChildNodes()->count());

        $this->assertEquals($node, $prunedNode);
        $this->assertNotSame($node->getChildNodes(), $prunedNode->getChildNodes());
        $this->assertEquals($node->getChildNodes(), $prunedNode->getChildNodes());
        $this->assertEquals(3, $prunedNode->getTotalChildNodesCount());
        $this->assertEquals(1, $prunedNode->getChildNodesCount());
    }

    /** @test */
    public function if_all_is_pruned_only_the_root_remains()
    {
        $node = $this->getNode();

        $prunedNode = $node->pruneChildNodes(function (Node $node) {
            return false;
        });

        $this->assertEquals($node->copyIsolatedNode(), $prunedNode);
    }

    /** @test */
    public function it_can_prune_by_specific_closure()
    {
        $node = $this->getNode();

        $prunedNode = $node->pruneChildNodes(function (Node $node) {
            return $node->getNodeEntry('id') == 3;
        });

        $this->assertEquals(
            (new DefaultNode(['id' => 1, 'name' => 'foobar']))->addChildNodes(new DefaultNode(['id' => 3, 'name' => 'second-child'])),
            $prunedNode
        );
    }

    /** @test */
    public function prune_maintains_the_ancestors_for_each_kept_node()
    {
        $node = $this->getNode();

        $prunedNode = $node->pruneChildNodes(function (Node $node) {
            return $node->getNodeEntry('id') == 3;
        });

        $this->assertEquals(
            (new DefaultNode(['id' => 1, 'name' => 'foobar']))->addChildNodes(new DefaultNode(['id' => 3, 'name' => 'second-child'])),
            $prunedNode
        );
    }

    /** @test */
    public function it_can_prune_a_node_collection()
    {
        $nodeCollection = $this->getNode()->getChildNodes();

        $prunedNodeCollection = $nodeCollection->prune(function (Node $node) {
            return $node->getNodeEntry('id') == 3;
        });

        $this->assertEquals(
            new \Thinktomorrow\Vine\NodeCollection(new DefaultNode(['id' => 3, 'name' => 'second-child'])),
            $prunedNodeCollection
        );
    }

    /**
     * @return Node
     */
    private function getNode()
    {
        $node = new DefaultNode(['id' => 1, 'name' => 'foobar']);
        $node->addChildNodes([$child = new DefaultNode(['id' => 2, 'name' => 'first-child'])]);
        $child->addChildNodes([$child2 = new DefaultNode(['id' => 3, 'name' => 'second-child'])]);
        $child->addChildNodes([$child3 = new DefaultNode(['id' => 4, 'name' => 'third-child'])]);

        return $node;
    }
}
