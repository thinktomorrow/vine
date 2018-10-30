<?php

use PHPUnit\Framework\TestCase;

class ArrayableNodeCollectionTest extends TestCase
{
    /** @test */
    public function it_can_count_the_values()
    {
        $collection = $this->getCollection();

        $this->assertCount(2, $collection);
    }

    /** @test */
    public function it_can_get_value_by_key()
    {
        $collection = $this->getCollection();

        $this->assertInstanceOf(\Vine\Node::class, $collection[1]);
    }

    /** @test */
    public function it_can_set_value_by_key()
    {
        $collection = $this->getCollection();
        $collection[2] = 'foobar';

        $this->assertCount(3, $collection);
        $this->assertEquals('foobar', $collection[2]);
    }

    /** @test */
    public function it_can_unset_a_value()
    {
        $collection = $this->getCollection();

        $this->assertCount(2, $collection);
        unset($collection[1]);

        $this->assertCount(1, $collection);
    }

    /** @test */
    public function it_can_check_if_key_exists()
    {
        $collection = $this->getCollection();

        $this->assertTrue(isset($collection[1]));
        $this->assertFalse(isset($collection[2]));
    }

    /** @test */
    public function it_can_loop_over_collection()
    {
        $collection = $this->getCollection();

        $flag = 0;
        foreach ($collection as $node) {
            $flag++;
        }

        $this->assertEquals(2, $flag);
    }

    /**
     * @return \Vine\NodeCollection
     */
    private function getCollection(): \Vine\NodeCollection
    {
        $collection = new \Vine\NodeCollection(
            new \Vine\Node(null),
            new \Vine\Node(null)
        );

        return $collection;
    }
}
