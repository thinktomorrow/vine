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

    public function __construct(string $id, ?string $parentId, array $values)
    {
        $this->id = $id;
        $this->parentId = $parentId;
        $this->values = $values;
        $this->children = new CustomNodeCollection();

        $this->idKey = 'id';
        $this->parentKey = 'parentId';
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
}
