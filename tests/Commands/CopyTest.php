<?php

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\FixtureSource;
use Vine\Node;
use Vine\Source;

class CopyTest extends TestCase
{
    /** @test */
    public function it_can_deep_copy_a_node()
    {
        $node = new Node(['id' => 1, 'name' => 'foobar']);
        $node->addChildren([$child = new Node(['id' => 2, 'name' => 'first-child'])]);
        $child->addChildren([$child2 = new Node(['id' => 3, 'name' => 'second-child'])]);
        $child->addChildren([$child3 = new Node(['id' => 4, 'name' => 'third-child'])]);

        $cloned = $node->copy();

        $this->assertNotSame($node, $cloned);
        $this->assertNotSame($node->getChildren()->first(), $cloned->getChildren()->first());
        $this->assertNotSame($node->getChildren()->first()->getChildren()->first(), $cloned->getChildren()->first()->getChildren()->first());
        $this->assertNotSame($node->getChildren()->first()->getChildren()[1], $cloned->getChildren()->first()->getChildren()[1]);
    }

    /** @test */
    public function it_can_get_new_node_with_specific_depth_of_childnodes()
    {
        $tree = (new \Vine\NodeCollectionFactory())->fromSource($this->getTranslation());

        $root = $tree->first()->getChildren()->first();
        $result = (new \Vine\Commands\Copy())->__invoke($root, 1);

        $this->assertNotSame($root, $result);
        $this->assertCount(4, $result->getChildren());
        foreach ($result->getChildren() as $child) {
            $this->assertCount(0, $child->getChildren());
        }
    }

    /** @test */
    public function node_can_be_isolated()
    {
        $root = new Node('foobar');
        $root->addChildren([$firstChild = new Node('first-child')]);
        $firstChild->addChildren([$secondChild = new Node('second-child')]);

        $isolatedNode = $firstChild->isolatedCopy();

        $this->assertTrue($isolatedNode->isRoot());
        $this->assertTrue($isolatedNode->isLeaf());
    }

    /** @test */
    public function collection_can_be_copied()
    {
        $root = new Node('foobar');
        $root2 = new Node('first-child');
        $root2->addChildren([new Node('second-child')]);

        $collection = new \Vine\NodeCollection($root, $root2);

        $copy = $collection->copy();

        $this->assertEquals($collection, $copy);
        $this->assertNotSame($collection, $copy);
    }

    /** @test */
    public function node_can_be_isolated_at_specified_depth()
    {
        $root = new Node('foobar');
        $root->addChildren([$firstChild = new Node('first-child')]);
        $firstChild->addChildren([$secondChild = new Node('second-child')]);

        $isolatedNode = $root->copy(1);

        $this->assertTrue($isolatedNode->isRoot());
        $this->assertCount(1, $isolatedNode->getChildren());
        $this->assertCount(0, $isolatedNode->getChildren()->first()->getChildren());
    }

    /**
     * @return Source
     */
    private function getTranslation(): Source
    {
        return new FixtureSource('default');
    }
}
