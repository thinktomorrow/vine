<?php

namespace Thinktomorrow\Vine\Presenters;

use RecursiveArrayIterator;
use RecursiveTreeIterator;

class CliPresenter extends ArrayPresenter implements Presenter
{
    public function render()
    {
        $output = PHP_EOL;
        $result = parent::render();

        $iterator = new RecursiveArrayIterator($result);
        $treeIterator = (new RecursiveTreeIterator(
            $iterator,
            RecursiveTreeIterator::BYPASS_KEY,
            \CachingIterator::CATCH_GET_CHILD,
            RecursiveTreeIterator::SELF_FIRST
        ));

        foreach ($treeIterator as $key => $value) {
            $output .= $value.PHP_EOL;
        }

        return $output;
    }
}
