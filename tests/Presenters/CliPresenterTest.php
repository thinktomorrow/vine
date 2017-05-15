<?php

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\FixtureTranslator;
use Tests\Implementations\Presenters\CliPresenter;
use Vine\Translators\Translator;

class CliPresenterTest extends TestCase
{
    /** @test */
    function it_can_represent_tree_in_terminal()
    {
        $tree = (new \Vine\TreeFactory)->create($this->getTranslation());

        $output = (new CliPresenter)->tree($tree)->render();

        $this->assertInternalType('string',$output);
        $this->assertStringStartsWith('|-root-1',trim($output,PHP_EOL));
    }

    /**
     * @return Translator
     */
    private function getTranslation(): Translator
    {
        return new FixtureTranslator('default');
    }
}