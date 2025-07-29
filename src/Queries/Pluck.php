<?php

namespace Thinktomorrow\Vine\Queries;

use Thinktomorrow\Vine\Node;

class Pluck
{
    /**
     * @param bool $down  | down: pluck from children, up: pluck from ancestors
     */
    public function __invoke(Node $node, string|int|\Closure $key, mixed $value = null, bool $down = true): array
    {
        $keyResult = $this->retrieveValueFromNode($node, $key);

        $values = [$keyResult];

        if ($value) {
            $valueResult = $this->retrieveValueFromNode($node, $value);
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

    private function retrieveValueFromNode(Node $node, $key)
    {
        if (is_callable($key)) {
            return call_user_func($key, $node);
        }

        return method_exists($node, $key) ? $node->{$key}() : $node->getNodeValue($key);
    }
}
