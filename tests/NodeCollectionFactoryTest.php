<?php

namespace Thinktomorrow\Vine\Tests;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\NodeCollection;
use Thinktomorrow\Vine\Sources\ArraySource;

class NodeCollectionFactoryTest extends TestCase
{
    /** @test */
    public function it_can_find_many_nodes_by_their_primary_identifiers()
    {
        $nodes = $this->getTree()->findMany('id', [5, 2]);

        $this->assertInstanceOf(NodeCollection::class, $nodes);
    }

    /** @test */
    public function it_can_find_a_node_by_its_primary_identifier()
    {
        $tree = $this->getTree();
        $node = $tree->find('id', 5);

        $this->assertSame($node, $tree[0]->getChildNodes()[1]->getChildNodes()[0]);
    }

    /** @test */
    public function it_can_get_total_of_nodes()
    {
        $this->assertEquals(5, $this->getTree()->total());
    }

    private function getTree(): NodeCollection
    {
        $records = [
            ['id' => 1, 'name' => 'foobar', 'parent_id' => 0],
            ['id' => 2, 'name' => 'foobar2', 'parent_id' => 1],
            ['id' => 3, 'name' => 'foobar3', 'parent_id' => 1],
            ['id' => 5, 'name' => 'foobar5', 'parent_id' => 3],
            ['id' => 4, 'name' => 'foobar4', 'parent_id' => 0],
        ];

        $translator = new ArraySource($records);

        return (new \Thinktomorrow\Vine\NodeCollectionFactory())->fromSource($translator);
    }
}
