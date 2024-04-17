<?php

namespace Thinktomorrow\Vine\Debug;

use Thinktomorrow\Vine\NodeCollection;

abstract class BasePresenter
{
    protected $collection;

    public function __construct()
    {
        $this->collection = new NodeCollection();
    }

    public function collection(NodeCollection $collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Render collection.
     *
     * @return string
     */
    abstract public function render();
}
