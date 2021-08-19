<?php

namespace Thinktomorrow\Vine\Queries;

use Thinktomorrow\Vine\DefaultNode;

class Pluck
{
    /**
     * @param DefaultNode $node
     * @param $key
     * @param null $value
     * @param bool $down  | down: pluck from children, up: pluck from ancestors
     *
     * @return array
     */
    public function __invoke(DefaultNode $node, $key, $value = null, $down = true): array
    {
        $values = $value
            ? [$node->getNodeEntry($key) => $node->getNodeEntry($value)]
            : [$node->getNodeEntry($key)];

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
