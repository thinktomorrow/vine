<?php

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\FixtureDataTransposer;
use Vine\Node;
use Vine\NodeCollection;
use Vine\DataTransposer;

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
     * @return DataTransposer
     */
    private function getTranslation(): DataTransposer
    {
        return new FixtureDataTransposer('default');
    }
}