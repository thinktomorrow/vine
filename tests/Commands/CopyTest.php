<?php

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\FixtureTransposer;
use Vine\Node;
use Vine\Transposable;

class CopyTest extends TestCase
{
    /** @test */
    function it_can_deep_copy_a_node()
    {
        $node = new Node(['id' => 1, 'name' => 'foobar']);
        $node->addChildren([$child = new Node(['id' => 2, 'name' => 'first-child'])]);
        $child->addChildren([$child2 = new Node(['id' => 3, 'name' => 'second-child'])]);
        $child->addChildren([$child3 = new Node(['id' => 4, 'name' => 'third-child'])]);

        $cloned = $node->copy();

        $this->assertNotSame($node, $cloned);
        $this->assertNotSame($node->children()->first(), $cloned->children()->first());
        $this->assertNotSame($node->children()->first()->children()->first(), $cloned->children()->first()->children()->first());
        $this->assertNotSame($node->children()->first()->children()[1], $cloned->children()->first()->children()[1]);

    }

    /** @test */
    function it_can_get_new_node_with_specific_depth_of_childnodes()
    {
        $tree = (new \Vine\TreeFactory)->create($this->getTranslation());

        $root = $tree->first()->children()->first();
        $result = (new \Vine\Commands\Copy())->__invoke($root,1);

        $this->assertNotSame($root,$result);
        $this->assertCount(4,$result->children());
        foreach($result->children() as $child)
        {
            $this->assertCount(0,$child->children());
        }
    }

    /** @test */
    function node_can_be_isolated()
    {
        $root = new Node('foobar');
        $root->addChildren([$firstChild = new Node('first-child')]);
        $firstChild->addChildren([$secondChild = new Node('second-child')]);

        $isolatedNode = $firstChild->isolatedCopy();

        $this->assertTrue($isolatedNode->isRoot());
        $this->assertTrue($isolatedNode->isLeaf());
    }

    /** @test */
    function node_can_be_isolated_at_specified_depth()
    {
        $root = new Node('foobar');
        $root->addChildren([$firstChild = new Node('first-child')]);
        $firstChild->addChildren([$secondChild = new Node('second-child')]);

        $isolatedNode = $root->copy(1);

        $this->assertTrue($isolatedNode->isRoot());
        $this->assertCount(1,$isolatedNode->children());
        $this->assertCount(0,$isolatedNode->children()->first()->children());
    }

    /**
     * @return Transposable
     */
    private function getTranslation(): Transposable
    {
        return new FixtureTransposer('default');
    }
}