<?php

namespace Vine\Presenters;

use RecursiveArrayIterator;
use RecursiveTreeIterator;

class CliPresenter extends ArrayPresenter implements Presenter
{
    public function render()
    {
        $output = PHP_EOL;
        $result = parent::render();

        $iterator = new RecursiveArrayIterator($result);
        $treeIterator = (new RecursiveTreeIterator($iterator));

        foreach($treeIterator as $key => $value)
        {
            $output .= $value.PHP_EOL;
        }

        return $output;
    }
}
