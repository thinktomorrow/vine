<?php

namespace Thinktomorrow\Vine;

interface WithNodeEntry
{
    public function getNodeEntry($key = null, $default = null);

    public function replaceNodeEntry($entry): void;

    public function hasNodeEntryValue($key, $value): bool;
}
