<?php

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\FixtureTranslator;
use Tests\Fixtures\LargeFixtureTranslator;
use Vine\Translators\Translator;

class PerformanceTest extends TestCase
{
    /** @test */
    function it_can_find_a_node_by_its_identifier()
    {
        $tree = (new \Vine\TreeFactory())->create($this->getTranslation());
        echo (new \Vine\Presenters\CliPresenter())->tree($tree)->render();
//        var_dump($tree->count());

//        var_dump($tree->roots()->first()->children()->first()->pluck(0,'2'));

        //$this->assertSame($node, $tree->roots()[0]->children()[1]->children()[0]);
    }

    /**
     * @return Translator
     */
    private function getTranslation(): Translator
    {
        return new FixtureTranslator('large');
    }
}
