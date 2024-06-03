<?php

namespace Thinktomorrow\Vine\Tests;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\Tests\Fixtures\CustomModelNode;
use Thinktomorrow\Vine\Tests\Fixtures\CustomNodeCollection;

class NodeModelTest extends TestCase
{
    /** @test */
    public function model_can_be_node()
    {
        $collection = CustomNodeCollection::fromIterable([
            $model1 = new CustomModelNode('1', null, ['name' => 'foobar']),
            $model2 = new CustomModelNode('2', '1', ['name' => 'foobar-2']),
            $model3 = new CustomModelNode('3', '2', ['name' => 'foobar-3']),
        ]);

        $this->assertCount(1, $collection->all());
        $this->assertEquals(3, $collection->total());

        $this->assertEquals($model1, $collection->findById('1'));
        $this->assertEquals($model2, $collection->findById('2'));
        $this->assertEquals($model3, $collection->findById('3'));

        $this->assertEquals($model1, $model2->getParentNode());
        $this->assertEquals($model3, $model2->getChildNodes()->first());
    }

    /** @test */
    public function model_can_be_referenced_via_object_memory()
    {
        $collection = CustomNodeCollection::fromIterable([
            $model1 = new CustomModelNode('1', null, ['name' => 'foobar']),
            $model2 = new CustomModelNode('2', '1', ['name' => 'foobar-2']),
            $model3 = new CustomModelNode('3', '2', ['name' => 'foobar-3']),
        ]);

        $this->assertEquals($model1, $model2->getParentNode());
        $this->assertEquals($model3, $model2->getChildNodes()->first());
    }
}
