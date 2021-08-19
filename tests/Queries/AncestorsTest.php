<?php

namespace Thinktomorrow\Vine\Tests\Queries;

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\FixtureSource;
use Thinktomorrow\Vine\DefaultNode;
use Thinktomorrow\Vine\NodeCollection;
use Thinktomorrow\Vine\Source;

class AncestorsTest extends TestCase
{
    /** @test */
    public function it_can_get_ancestors()
    {
        $root = new DefaultNode('foobar');
        $root->addChildNodes([$firstChild = new DefaultNode('first-child')]);
        $firstChild->addChildNodes([$secondChild = new DefaultNode('second-child')]);

        $ancestors = (new \Thinktomorrow\Vine\Queries\Ancestors())->__invoke($secondChild);

        $this->assertCount(2, $ancestors);
        $this->assertEquals(new NodeCollection(...[$root, $firstChild]), $ancestors);
    }

    /** @test */
    public function it_can_get_ancestors_at_certain_depth()
    {
        $root = new DefaultNode('foobar');
        $root->addChildNodes([$firstChild = new DefaultNode('first-child')]);
        $firstChild->addChildNodes([$secondChild = new DefaultNode('second-child')]);

        $ancestors = (new \Thinktomorrow\Vine\Queries\Ancestors())->__invoke($secondChild, 1);

        $this->assertCount(1, $ancestors);
        $this->assertEquals(new NodeCollection(...[$firstChild]), $ancestors);
    }

    /** @test */
    public function node_can_get_the_ancestor_tree()
    {
        $root = new DefaultNode('foobar');
        $root->addChildNodes([$firstChild = new DefaultNode('first-child')]);
        $firstChild->addChildNodes([$secondChild = new DefaultNode('second-child')]);

        $ancestors = $secondChild->getAncestorNodes();

        $this->assertCount(2, $ancestors);
        $this->assertEquals(new NodeCollection(...[$root, $firstChild]), $ancestors);
    }

    /**
     * @return \Thinktomorrow\Vine\Source
     */
    private function getTranslation(): Source
    {
        return new FixtureSource('default');
    }
}
