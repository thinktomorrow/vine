<?php

namespace Thinktomorrow\Vine\Debug;

use Thinktomorrow\Vine\NodeCollection;

class Debugger
{
    private NodeCollection $collection;

    public function __construct()
    {

    }

    public function collection(NodeCollection $collection)
    {
        $this->collection = $collection;

        return $this;
    }

    public function render()
    {
        $presenter = new AsciiPresenter();
    }
}
