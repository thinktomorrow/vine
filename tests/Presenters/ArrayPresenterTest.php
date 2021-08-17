<?php

namespace Thinktomorrow\Vine\Tests\Presenters;

use Thinktomorrow\Vine\NodeCollection;
use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\NodeCollectionFactory;
use Thinktomorrow\Vine\Tests\Fixtures\FixtureSource;
use Thinktomorrow\Vine\DefaultNode;
use Thinktomorrow\Vine\Presenters\ArrayPresenter;
use Thinktomorrow\Vine\Source;

class ArrayPresenterTest extends TestCase
{
    /** @test */
    public function it_can_represent_a_collection_as_array()
    {
        $result = (new ArrayPresenter())->collection(new NodeCollection(new DefaultNode(['id' => 1])))->render();

        $this->assertInternalType('array', $result);
    }

    /** @test */
    public function it_can_use_tree_as_source()
    {
        $tree = (new NodeCollectionFactory())->fromSource($this->getTranslation());

        $result = (new ArrayPresenter())->collection($tree)->render();

        $this->assertInternalType('array', $result);
    }

    /**
     * @return Source
     */
    private function getTranslation(): Source
    {
        return new FixtureSource('default');
    }
}
