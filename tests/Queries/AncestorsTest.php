<?php

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\FixtureTransposer;
use Vine\Node;
use Vine\NodeCollection;
use Vine\Transposable;

class AncestorsTest extends TestCase
{
    /** @test */
    function it_can_get_ancestors()
    {
        $root = new Node('foobar');
        $root->addChildren([$firstChild = new Node('first-child')]);
        $firstChild->addChildren([$secondChild = new Node('second-child')]);

        $ancestors = (new \Vine\Queries\Ancestors())->__invoke($secondChild);

        $this->assertCount(2,$ancestors);
        $this->assertEquals(new NodeCollection(...[ $root, $firstChild ]),$ancestors);

    }

    /** @test */
    function it_can_get_ancestors_at_certain_depth()
    {
        $root = new Node('foobar');
        $root->addChildren([$firstChild = new Node('first-child')]);
        $firstChild->addChildren([$secondChild = new Node('second-child')]);

        $ancestors = (new \Vine\Queries\Ancestors())->__invoke($secondChild,1);

        $this->assertCount(1,$ancestors);
        $this->assertEquals(new NodeCollection(...[ $firstChild ]),$ancestors);

    }

    /** @test */
    function node_can_get_the_ancestor_tree()
    {
        $root = new Node('foobar');
        $root->addChildren([$firstChild = new Node('first-child')]);
        $firstChild->addChildren([$secondChild = new Node('second-child')]);

        $ancestors = $secondChild->ancestors();

        $this->assertCount(2,$ancestors);
        $this->assertEquals(new NodeCollection(...[ $root, $firstChild ]),$ancestors);
    }

    /**
     * @return Transposable
     */
    private function getTranslation(): Transposable
    {
        return new FixtureTransposer('default');
    }
}