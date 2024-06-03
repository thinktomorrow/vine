<?php
declare(strict_types=1);

namespace Thinktomorrow\Vine;

use Thinktomorrow\Vine\Commands\Copy;
use Thinktomorrow\Vine\Commands\Move;
use Thinktomorrow\Vine\Queries\Ancestors;
use Thinktomorrow\Vine\Queries\Count;
use Thinktomorrow\Vine\Queries\Pluck;

class DefaultNode implements Node, WithNodeEntry
{
    use NodeDefaults;

    protected $entry;

    public function __construct($entry, ?NodeCollection $children = null, string $idKey = 'id', string $parentKey = 'parent_id')
    {
        $this->idKey = $idKey;
        $this->parentKey = $parentKey;

        $this->replaceNodeEntry($entry);

        $this->children = $children ?: new NodeCollection();
    }


    public function getNodeId(): string
    {
        return (string) $this->getNodeEntry($this->idKey);
    }

    public function getParentNodeId(): ?string
    {
        if ($parentId = $this->getNodeEntry($this->parentKey)) {
            return (string) $parentId;
        }

        return null;
    }

    /**
     * DefaultNode has its model entry as a property
     *
     * @param $key
     * @param $default
     * @return mixed|null
     */
    public function getNodeEntry($key = null, $default = null)
    {
        if (! ($key === null)) {
            if (is_array($this->entry)) {
                return isset($this->entry[$key]) ? $this->entry[$key] : $default;
            }

            return isset($this->entry->{$key}) ? $this->entry->{$key} : $default;
        }

        return $this->entry;
    }

    public function replaceNodeEntry($entry): void
    {
        $this->entry = $entry;
    }

    public function hasNodeEntryValue($key, $value): bool
    {
        return in_array($this->getNodeEntry($key), (array) $value);
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
