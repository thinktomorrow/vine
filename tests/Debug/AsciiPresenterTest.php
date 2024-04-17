<?php

namespace Thinktomorrow\Vine\Tests\Debug;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\NodeCollection;
use Thinktomorrow\Vine\Debug\AsciiPresenter;
use Thinktomorrow\Vine\Tests\Fixtures\FixtureSource;

class AsciiPresenterTest extends TestCase
{
    /** @test */
    public function it_can_represent_tree_as_ascii()
    {
        $tree = $this->getCollection();

        $output = (new AsciiPresenter())->render($tree);

$expectedOutput = "-root-1
-root-2
 \
 |-child-1
  \
  |-child-1-1
  |-child-1-2
  |-child-1-3
  |-child-1-4
   \
   |-child-2-1
   |-child-2-2
   |-child-2-3
   |-child-2-4
   |-child-2-5
    \
    |-child-3-1
    |-child-3-2
     \
     |-child-4-1
 \
 |-child-1-5";

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
