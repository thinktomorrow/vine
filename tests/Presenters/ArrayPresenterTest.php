<?php

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\FixtureTranslator;
use Vine\Node;
use Vine\Translators\Translator;

class ArrayPresenterTest extends TestCase
{
    /** @test */
    function it_can_represent_tree_as_array()
    {
        $tree = (new \Vine\TreeFactory)->create($this->getTranslation());

        $result = (new \Vine\Presenters\ArrayPresenter)->tree($tree)->render();

        $this->assertInternalType('array',$result);
    }

    /**
     * @return Translator
     */
    private function getTranslation(): Translator
    {
        return new FixtureTranslator('default');
    }
}