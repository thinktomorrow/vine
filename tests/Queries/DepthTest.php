<?php

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\FixtureTranslator;
use Vine\Translators\Translator;

class DepthTest extends TestCase
{
    /** @test */
    function it_can_get_new_node_with_specific_depth_of_childnodes()
    {
        $tree = (new \Vine\TreeFactory)->create($this->getTranslation());

        $root = $tree->roots()->first()->children()->first();
        $result = (new \Vine\Queries\Depth())->__invoke($root,1);

        $this->assertNotSame($root,$result);
        $this->assertCount(4,$result->children());
        foreach($result->children() as $child)
        {
            $this->assertCount(0,$child->children());
        }

    }

    /**
     * @return Translator
     */
    private function getTranslation(): Translator
    {
        return new FixtureTranslator('default');
    }
}