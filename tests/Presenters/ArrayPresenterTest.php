<?php

namespace Thinktomorrow\Vine\Tests\Presenters;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\DefaultNode;
use Thinktomorrow\Vine\NodeCollection;
use Thinktomorrow\Vine\NodeCollectionFactory;
use Thinktomorrow\Vine\Presenters\ArrayPresenter;
use Thinktomorrow\Vine\Source;
use Thinktomorrow\Vine\Tests\Fixtures\FixtureSource;

class ArrayPresenterTest extends TestCase
{
    /** @test */
    public function it_can_represent_a_collection_as_array()
    {
        $result = (new ArrayPresenter())->collection(new NodeCollection([new DefaultNode(['id' => 1])]))->render();

        $this->assertIsArray($result);
    }

    /** @test */
    public function it_can_use_tree_as_source()
    {
        $tree = (new NodeCollectionFactory())->fromSource($this->getTranslation());

        $result = (new ArrayPresenter())->collection($tree)->render();

        $this->assertIsArray($result);
    }

    /**
     * @return Source
     */
    private function getTranslation(): Source
    {
        return new FixtureSource('default');
    }
}
