<?php

namespace Thinktomorrow\Vine;

/**
 * Contract for converting a flat adjacent data set to
 * the desired tree structure.
 */
interface Source
{
    /**
     * Full array of original data rows
     * These are the rows to be converted to the tree model.
     *
     * @return iterable
     */
    public function nodeEntries(): iterable;

    public function createNode($entry): Node;
}
