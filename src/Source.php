<?php

namespace Vine;

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
     * @return array
     */
    public function nodeEntries(): array;

    /**
     * Attribute key of the primary identifier of each row. e.g. 'id'.
     *
     * @return string
     */
    public function nodeKeyIdentifier(): string;

    /**
     * Attribute key of the parent foreign identifier of each row. e.g. 'parent_id'.
     *
     * @return string
     */
    public function nodeParentKeyIdentifier(): string;
}
