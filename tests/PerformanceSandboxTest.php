<?php

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\FixtureTranslator;
use Tests\Fixtures\LargeFixtureTranslator;
use Vine\Translators\Translator;

class PerformanceSandboxTest extends TestCase
{
    /** @test */
    function it_can_find_a_node_by_its_identifier()
    {
        $tree = (new \Vine\TreeFactory())->create($this->getTranslation());

        // This is a useless assertion but we keep this testclass to easily sandbox with our large dataset.
        $this->assertTrue($tree->count() > 1000);
    }

    /**
     * @return Translator
     */
    private function getTranslation(): Translator
    {
        return new FixtureTranslator('large');
    }
}
