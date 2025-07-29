<?php
declare(strict_types=1);

namespace Thinktomorrow\Vine;

use Thinktomorrow\Vine\Debug\Arrayable;

class DefaultNode implements Node, Arrayable
{
    use NodeDefaults;

    protected $entry;
    protected string $idKey;
    protected string $parentKey;

    public function __construct($entry, ?NodeCollection $children = null, string $idKey = 'id', string $parentKey = 'parent_id')
    {
        $this->idKey = $idKey;
        $this->parentKey = $parentKey;

        $this->replaceNodeEntry($entry);

        $this->children = $children ?: new NodeCollection();
    }

    protected function getNodeIdKey(): string
    {
        return $this->idKey;
    }

    protected function getParentNodeIdKey(): string
    {
        return $this->parentKey;
    }

    public function getNodeId(): string
    {
        return (string) $this->getNodeValue($this->getNodeIdKey());
    }

    public function getParentNodeId(): ?string
    {
        if ($parentId = $this->getNodeValue($this->getParentNodeIdKey())) {
            return (string) $parentId;
        }

        return null;
    }



    /**
     * DefaultNode has its model entry as a property
     *
     * @return mixed|null
     */
    public function getNodeEntry()
    {
        return $this->entry;
    }

    /**
     * Specific to DefaultNode where the entry is a property
     * @param $entry
     * @return void
     */
    public function replaceNodeEntry($entry): void
    {
        $this->entry = $entry;
    }

    public function hasNodeValue($key, $value): bool
    {
        return in_array($this->getNodeValue($key), (array) $value);
    }

    public function getNodeValue($key, $default = null): mixed
    {
        if (is_array($this->entry)) {
            return isset($this->entry[$key]) ? $this->entry[$key] : $default;
        }

        return isset($this->entry->{$key}) ? $this->entry->{$key} : $default;
    }

    public function getSortValue($key)
    {
        return $this->getNodeValue($key);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getNodeId(),
            'parent_id' => $this->getParentNodeId(),
            'entry' => $this->getNodeEntry(),
            'children' => $this->getChildNodes()->toArray(),
        ];
    }
}
