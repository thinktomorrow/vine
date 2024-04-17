<?php

namespace Thinktomorrow\Vine\Debug;

use Thinktomorrow\Vine\NodeCollection;

class ArrayPresenter
{
    /**
     * Render html filter tree.
     *
     * @return array
     */
    public function render(NodeCollection $collection): array
    {
        return $collection->toArray();
    }
}
