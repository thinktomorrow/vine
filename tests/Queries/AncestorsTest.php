<?php

namespace Thinktomorrow\Vine\Tests\Queries;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\DefaultNode;
use Thinktomorrow\Vine\NodeCollection;
use Thinktomorrow\Vine\Tests\Fixtures\FixtureSource;

class AncestorsTest extends TestCase
{
    public function test_it_can_get_ancestors()
    {
        $root = new DefaultNode('foobar');
        $root->addChildNodes([$firstChild = new DefaultNode('first-child')]);
        $firstChild->addChildNodes([$secondChild = new DefaultNode('second-child')]);

        $ancestors = (new \Thinktomorrow\Vine\Queries\Ancestors())->__invoke($secondChild);

        $this->assertCount(2, $ancestors);
        $this->assertEquals(new NodeCollection([$root, $firstChild]), $ancestors);
    }

    public function test_it_can_get_ancestors_at_certain_depth()
    {
        $root = new DefaultNode('foobar');
        $root->addChildNodes([$firstChild = new DefaultNode('first-child')]);
        $firstChild->addChildNodes([$secondChild = new DefaultNode('second-child')]);

        $ancestors = (new \Thinktomorrow\Vine\Queries\Ancestors())->__invoke($secondChild, 1);

        $this->assertCount(1, $ancestors);
        $this->assertEquals(new NodeCollection([$firstChild]), $ancestors);
    }

    public function test_node_can_get_the_ancestor_tree()
    {
        $root = new DefaultNode('foobar');
        $root->addChildNodes([$firstChild = new DefaultNode('first-child')]);
        $firstChild->addChildNodes([$secondChild = new DefaultNode('second-child')]);

        $ancestors = $secondChild->getAncestorNodes();

        $this->assertCount(2, $ancestors);
        $this->assertEquals(new NodeCollection([$root, $firstChild]), $ancestors);
    }

    public function test_it_can_get_root_node()
    {
        $root = new DefaultNode('foobar');
        $root->addChildNodes([$firstChild = new DefaultNode('first-child')]);
        $firstChild->addChildNodes([$secondChild = new DefaultNode('second-child')]);

        $this->assertEquals($root, $firstChild->getRootNode());
        $this->assertEquals($root, $secondChild->getRootNode());
        $this->assertEquals($root, $root->getRootNode());
    }

    private function getTranslation(): NodeCollection
    {
        return (new FixtureSource('default'))->getAsCollection();
    }
}
