<?php

namespace Thinktomorrow\Vine\Presenters;

use Thinktomorrow\Vine\NodeCollection;

interface Presenter
{
    public function collection(NodeCollection $collection);

    public function render();
}
