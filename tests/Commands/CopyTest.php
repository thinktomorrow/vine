<?php

namespace Thinktomorrow\Vine\Tests\Commands;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\DefaultNode;
use Thinktomorrow\Vine\NodeCollection;
use Thinktomorrow\Vine\Tests\Fixtures\FixtureSource;

class CopyTest extends TestCase
{
    /** @test */
    public function it_can_deep_copy_a_node()
    {
        $node = new DefaultNode(['id' => 1, 'name' => 'foobar']);
        $node->addChildNodes([$child = new DefaultNode(['id' => 2, 'name' => 'first-child'])]);
        $child->addChildNodes([$child2 = new DefaultNode(['id' => 3, 'name' => 'second-child'])]);
        $child->addChildNodes([$child3 = new DefaultNode(['id' => 4, 'name' => 'third-child'])]);

        $cloned = $node->copyNode();

        $this->assertNotSame($node, $cloned);
        $this->assertNotSame($node->getChildNodes()->first(), $cloned->getChildNodes()->first());
        $this->assertNotSame($node->getChildNodes()->first()->getChildNodes()->first(), $cloned->getChildNodes()->first()->getChildNodes()->first());
        $this->assertNotSame($node->getChildNodes()->first()->getChildNodes()[1], $cloned->getChildNodes()->first()->getChildNodes()[1]);
    }

    /** @test */
    public function it_can_get_new_node_with_specific_depth_of_childnodes()
    {
        $tree = $this->getTranslation();

        $root = $tree->first()->getChildNodes()->first();
        $result = (new \Thinktomorrow\Vine\Commands\Copy())->__invoke($root, 1);

        $this->assertNotSame($root, $result);
        $this->assertCount(4, $result->getChildNodes());
        foreach ($result->getChildNodes() as $child) {
            $this->assertCount(0, $child->getChildNodes());
        }
    }

    /** @test */
    public function node_can_be_isolated()
    {
        $root = new DefaultNode('foobar');
        $root->addChildNodes([$firstChild = new DefaultNode('first-child')]);
        $firstChild->addChildNodes([$secondChild = new DefaultNode('second-child')]);

        $isolatedNode = $firstChild->copyIsolatedNode();

        $this->assertTrue($isolatedNode->isRootNode());
        $this->assertTrue($isolatedNode->isLeafNode());
    }

    /** @test */
    public function collection_can_be_copied()
    {
        $root = new DefaultNode('foobar');
        $root2 = new DefaultNode('first-child');
        $root2->addChildNodes([new DefaultNode('second-child')]);

        $collection = new \Thinktomorrow\Vine\NodeCollection([$root, $root2]);

        $copy = $collection->copy();

        $this->assertEquals($collection, $copy);
        $this->assertNotSame($collection, $copy);
    }

    /** @test */
    public function node_can_be_isolated_at_specified_depth()
    {
        $root = new DefaultNode('foobar');
        $root->addChildNodes([$firstChild = new DefaultNode('first-child')]);
        $firstChild->addChildNodes([$secondChild = new DefaultNode('second-child')]);

        $isolatedNode = $root->copyNode(1);

        $this->assertTrue($isolatedNode->isRootNode());
        $this->assertCount(1, $isolatedNode->getChildNodes());
        $this->assertCount(0, $isolatedNode->getChildNodes()->first()->getChildNodes());
    }

    private function getTranslation(): NodeCollection
    {
        return (new FixtureSource('default'))->getAsCollection();
    }
}
