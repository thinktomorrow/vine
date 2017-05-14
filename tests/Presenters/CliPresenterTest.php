<?php

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\FixtureTranslator;
use Vine\Node;
use Vine\Translators\Translator;

class CliPresenterTest extends TestCase
{
    /** @test */
    function it_can_represent_tree_in_terminal()
    {
        $tree = (new \Vine\TreeFactory)->create($this->getTranslation());

        $output = (new \Vine\Presenters\CliPresenter)->tree($tree)->render();

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