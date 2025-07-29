<?php

namespace Thinktomorrow\Vine\Tests\Fixtures;

use Thinktomorrow\Vine\Node;
use Thinktomorrow\Vine\NodeDefaults;

class CustomModelNode implements Node
{
    use NodeDefaults;

    private array $values;
    protected string $id;
    protected ?string $parentId;

    public function __construct(string $id, ?string $parentId = null, array $values = [])
    {
        $this->id = $id;
        $this->parentId = $parentId;
        $this->values = $values;
        $this->children = new CustomNodeCollection();
    }

    protected function getNodeIdKey(): string
    {
        return 'id';
    }

    protected function getParentNodeIdKey(): string
    {
        return 'parentId';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parentId,
            'values' => $this->values,
            'children' => $this->getChildNodes()->toArray(),
        ];
    }

    public function changeValue(string $key, $value): void
    {
        $this->values[$key] = $value;
    }

    public function getNodeValue($key, $default = null): mixed
    {
        return $this->values[$key] ?? $default;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
