<?php

namespace Thinktomorrow\Vine\Queries;

use Thinktomorrow\Vine\Node;

class Pluck
{
    /**
     * @param Node $node
     * @param string $key
     * @param null $value
     * @param bool $down  | down: pluck from children, up: pluck from ancestors
     *
     * @return array
     */
    public function __invoke(Node $node, string $key, $value = null, $down = true): array
    {
        $keyResult = method_exists($node, $key) ? $node->{$key}() : $node->getNodeEntry($key);

        $values = [$keyResult];

        if ($value) {
            $valueResult = method_exists($node, $value) ? $node->{$value}() : $node->getNodeEntry($value);
            $values = [$keyResult => $valueResult];
        }

        $nodes = $down ? $node->getChildNodes() : [$node->getParentNode()];

        foreach ($nodes as $node) {
            // If node entry is empty, which means there is no parent, we bail out
            if (! $node) {
                break;
            }

            $values = $value
                ? array_replace($values, $this->__invoke($node, $key, $value, $down)) // Respect the passed key values
                : array_merge($values, $this->__invoke($node, $key, $value, $down)); // No key values specified so just append
        }

        return $values;
    }
}
