<?php

namespace Vine\Queries;

use Vine\Node;

class Pluck
{
    /**
     * @param Node $node
     * @param $key
     * @param null $value
     * @return array
     */
    public function __invoke(Node $node, $key, $value = null): array
    {
        $values = $value
            ? [$node->entry($key) => $node->entry($value)]
            : [$node->entry($key)];

        foreach($node->children() as $child)
        {
            $values = $value
                ? array_replace($values, $this->__invoke($child, $key, $value)) // Respect the passed key values
                : array_merge($values, $this->__invoke($child, $key, $value)); // No key values specified so just append
        }

        return $values;
    }
}