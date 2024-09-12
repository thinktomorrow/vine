<?php

namespace Thinktomorrow\Vine\Debug;

use Thinktomorrow\Vine\NodeCollection;

class Debugger
{
    private ?NodeCollection $collection = null;
    private string $type = 'array';

    public function collection(NodeCollection $collection)
    {
        $this->collection = $collection;

        return $this;
    }

    public function asAscii(): static
    {
        $this->type = 'ascii';

        return $this;
    }

    public function asArray(): static
    {
        $this->type = 'array';

        return $this;
    }

    public function render()
    {
        if ($this->type == 'ascii') {
            return (new AsciiPresenter())->render($this->collection);
        }

        return (new ArrayPresenter())->render($this->collection);
    }

    public function __toString()
    {
        return $this->asAscii()->render();
    }
}
