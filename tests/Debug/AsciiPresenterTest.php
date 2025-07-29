<?php

namespace Thinktomorrow\Vine\Tests\Debug;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\Debug\AsciiPresenter;
use Thinktomorrow\Vine\NodeCollection;
use Thinktomorrow\Vine\Tests\Fixtures\FixtureSource;

class AsciiPresenterTest extends TestCase
{
    public function test_it_can_represent_tree_as_ascii()
    {
        $tree = $this->getCollection();

        $output = (new AsciiPresenter())->render($tree);

        $expectedOutput = "-1-15\|-2\|-3|-4|-5|-6\|-7|-8|-9|-10|-11\|-12|-13\|-14\|-16";

        $this->assertEquals($this->normalizeString($expectedOutput), $this->normalizeString($output));
    }

    private function normalizeString($string)
    {
        // Strip out all extra whitespace and newlines
        return preg_replace('/\s+/', '', $string);
    }

    private function getCollection(): NodeCollection
    {
        return (new FixtureSource('default'))->getAsCollection();
    }
}
