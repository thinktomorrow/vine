<?php

namespace Vine\Presenters;

use Vine\Tree;

interface Presenter
{
    public function tree(Tree $tree);

    public function render();
}