<?php

namespace Thinktomorrow\Vine\Tests\Commands;

use Thinktomorrow\Vine\Node;
use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\DefaultNode;
use Thinktomorrow\Vine\NodeCollection;

class ShakeTest extends TestCase
{
    /** @test */
    public function a_node_collection_that_does_not_need_shaking_is_copied_but_has_exact_same_structure()
    {
        $node = $this->getNode();

        $shakedNode = $node->shakeChildNodes(function (Node $node) {
            return true;
        });

        // Original is preserved
        $this->assertEquals(1, $node->getChildNodes()->count());
        $this->assertEquals(2, $node->getChildNodes()->first()->getChildNodes()->count());

        $this->assertEquals($node, $shakedNode);
        $this->assertNotSame($node->getChildNodes(), $shakedNode->getChildNodes());
        $this->assertEquals($node->getChildNodes(), $shakedNode->getChildNodes());
        $this->assertEquals(3, $shakedNode->getTotalChildNodesCount());
        $this->assertEquals(1, $shakedNode->getChildNodesCount());
    }

    /** @test */
    public function if_all_is_shaken_only_the_root_remains()
    {
        $node = $this->getNode();

        $shakedNode = $node->shakeChildNodes(function (Node $node) {
            return false;
        });

        $this->assertEquals($node->copyIsolatedNode(), $shakedNode);
    }

    /** @test */
    public function shake_maintains_the_ancestors_for_each_kept_node()
    {
        $node = $this->getNode();

        $shakedNode = $node->shakeChildNodes(function (Node $node) {
            return $node->getNodeEntry('id') == 3;
        });

        $this->assertEquals(
            (new DefaultNode(['id' => 1, 'name' => 'foobar']))
                ->addChildNodes(
                    (new DefaultNode(['id' => 2, 'name' => 'first-child']))
                        ->addChildNodes(new DefaultNode(['id' => 3, 'name' => 'second-child']))
                ),
            $shakedNode
        );
    }

    /** @test */
    public function it_can_shake_a_node_collection()
    {
        $nodeCollection = $this->getNode()->getChildNodes();

        $shakedNodeCollection = $nodeCollection->shake(function (Node $node) {
            return $node->getNodeEntry('id') == 3;
        });

        $this->assertEquals(
            new NodeCollection((new DefaultNode(['id' => 2, 'name' => 'first-child']))
                ->addChildNodes(new DefaultNode(['id' => 3, 'name' => 'second-child']))
            ),
            $shakedNodeCollection
        );
    }

    /**
     * @return DefaultNode
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
