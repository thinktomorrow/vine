<?php

namespace Thinktomorrow\Vine\Debug;

use Thinktomorrow\Vine\Node;
use Thinktomorrow\Vine\NodeCollection;

class AsciiPresenter
{
    public function render(NodeCollection $collection)
    {
        $output = PHP_EOL;

        $previousLevel = 0;
        $collection->eachRecursive(function (Node $node) use (&$output, &$previousLevel) {
            $level = $node->getNodeDepth();

            if ($level != $previousLevel && $level > 0) {
                $output .= str_repeat(' ', $level).'\\'.PHP_EOL;
            }

            $output .= str_repeat(' ', $level). ($level > 0 ? '|-' : '-').$node->getNodeId().PHP_EOL;
            $previousLevel = $level;
        });

        return $output;
    }
}
