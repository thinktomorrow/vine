<?php

namespace Vine\Queries;

use Vine\Node;

class Pluck
{
    /**
     * @param Node $node
     * @param $key
     * @param null $value
     * @param bool $down  | down: pluck from children, up: pluck from ancestors
     *
     * @return array
     */
    public function __invoke(Node $node, $key, $value = null, $down = true): array
    {
        $values = $value
            ? [$node->entry($key) => $node->entry($value)]
            : [$node->entry($key)];

        $nodes = $down ? $node->children() : [$node->parent()];

        foreach ($nodes as $node) {
            // If node entry is empty, which means there is no parent, we bail out
            if (!$node) {
                break;
            }

            $values = $value
                ? array_replace($values, $this->__invoke($node, $key, $value, $down)) // Respect the passed key values
                : array_merge($values, $this->__invoke($node, $key, $value, $down)); // No key values specified so just append
        }

        return $values;
    }
}
