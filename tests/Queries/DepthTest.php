<?php

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\FixtureTranslator;
use Vine\Node;
use Vine\Translators\Translator;

class DepthTest extends TestCase
{
    /** @test */
    function it_can_get_new_node_with_specific_depth_of_childnodes()
    {
        $tree = (new \Vine\TreeFactory)->create($this->getTranslation());

        $root = $tree->roots()->first()->children()->first();
        $result = (new \Vine\Queries\Depth())->__invoke($root,1);

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

        $isolatedNode = $root->isolatedCopy(1);

        $this->assertTrue($isolatedNode->isRoot());
        $this->assertCount(1,$isolatedNode->children());
        $this->assertCount(0,$isolatedNode->children()->first()->children());
    }

    /**
     * @return Translator
     */
    private function getTranslation(): Translator
    {
        return new FixtureTranslator('default');
    }
}