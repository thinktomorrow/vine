<?php

namespace Thinktomorrow\Vine\Presenters;

use RecursiveArrayIterator;
use RecursiveTreeIterator;

class CliPresenter extends ArrayPresenter
{
    public function render()
    {
        $output = PHP_EOL;

        $collection = $this->collection->toArray();

        $iterator = new RecursiveArrayIterator($collection);
        $treeIterator = (new RecursiveTreeIterator(
            $iterator,
            RecursiveTreeIterator::BYPASS_KEY,
            RecursiveTreeIterator::CATCH_GET_CHILD,
            RecursiveTreeIterator::SELF_FIRST,
        ));

        foreach ($treeIterator as $key => $value) {
            $output .= $value.PHP_EOL;
        }

        return $output;
    }
}
