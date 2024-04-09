<?php

namespace Thinktomorrow\Vine\Tests;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\DefaultNode;
use Thinktomorrow\Vine\NodeCollection;

class ArrayableNodeCollectionTest extends TestCase
{
    public function test_it_can_count_the_values()
    {
        $collection = $this->getCollection();

        $this->assertCount(2, $collection);
    }

    public function test_it_can_get_value_by_key()
    {
        $collection = $this->getCollection();

        $this->assertInstanceOf(\Thinktomorrow\Vine\DefaultNode::class, $collection[1]);
    }

    public function test_it_can_set_value_by_key()
    {
        $collection = $this->getCollection();
        $collection[2] = 'foobar';

        $this->assertCount(3, $collection);
        $this->assertEquals('foobar', $collection[2]);
    }

    public function test_it_can_unset_a_value()
    {
        $collection = $this->getCollection();

        $this->assertCount(2, $collection);
        unset($collection[1]);

        $this->assertCount(1, $collection);
    }

    public function test_it_can_check_if_key_exists()
    {
        $collection = $this->getCollection();

        $this->assertTrue(isset($collection[1]));
        $this->assertFalse(isset($collection[2]));
    }

    public function test_it_can_loop_over_collection()
    {
        $collection = $this->getCollection();

        $flag = 0;
        foreach ($collection as $node) {
            $flag++;
        }

        $this->assertEquals(2, $flag);
    }

    public function test_it_can_export_to_array()
    {
        $collection = $this->getCollection();

        $array = $collection->toArray();

        $this->assertIsArray($array);

        // TODO: test to array of node as well
    }

    /**
     * @return NodeCollection
     */
    private function getCollection(): NodeCollection
    {
        return NodeCollection::fromArray([
            new DefaultNode(['id' => 1]),
            new DefaultNode(['id' => 2]),
            new DefaultNode(['id' => 3, 'parent_id' => 2]),
        ]);
    }
}
