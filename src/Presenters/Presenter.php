<?php

namespace Vine\Presenters;

use Vine\NodeCollection;

interface Presenter
{
    public function collection(NodeCollection $collection);

    public function render();
}
