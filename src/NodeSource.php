<?php

namespace Thinktomorrow\Vine;

interface NodeSource
{
    public function getNodeId(): string;

    public function getParentNodeId(): ?string;
}
