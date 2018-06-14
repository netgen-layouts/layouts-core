<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use Netgen\BlockManager\API\Values\Collection\Collection as APICollection;
use Netgen\BlockManager\API\Values\Collection\Item as APIItem;
use Netgen\BlockManager\API\Values\Collection\Query as APIQuery;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Item\Item as CmsItem;

final class Collection implements APICollection
{
    /**
     * @var array
     */
    private $manualItems = [];

    /**
     * @var array
     */
    private $overrideItems = [];

    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Query
     */
    private $query;

    public function __construct(
        array $manualItems = [],
        array $overrideItems = [],
        array $queryValues = null,
        int $queryCount = 0
    ) {
        foreach ($manualItems as $position => $value) {
            $this->manualItems[$position] = new Item(
                [
                    'type' => Item::TYPE_MANUAL,
                    'value' => $value,
                    'cmsItem' => new CmsItem(['value' => $value]),
                    'position' => $position,
                    'isValid' => $value !== null,
                ]
            );
        }

        foreach ($overrideItems as $position => $value) {
            $this->overrideItems[$position] = new Item(
                [
                    'type' => Item::TYPE_OVERRIDE,
                    'value' => $value,
                    'cmsItem' => new CmsItem(['value' => $value]),
                    'position' => $position,
                    'isValid' => $value !== null,
                ]
            );
        }

        if ($queryValues !== null) {
            $this->query = new Query(
                [
                    'queryType' => new QueryType(
                        'my_query_type',
                        $queryValues,
                        $queryCount
                    ),
                ]
            );
        }
    }

    public function getId()
    {
    }

    public function getStatus(): int
    {
    }

    public function getType(): string
    {
    }

    public function isDraft(): bool
    {
    }

    public function isPublished(): bool
    {
    }

    public function isArchived(): bool
    {
    }

    public function getOffset(): int
    {
    }

    public function getLimit(): ?int
    {
    }

    public function hasItem($position, $type = null): bool
    {
        return $this->hasManualItem($position) || $this->hasOverrideItem($position);
    }

    public function getItem($position, $type = null): ?APIItem
    {
        $item = $this->getManualItem($position);
        if ($item !== null) {
            return $item;
        }

        return $this->getOverrideItem($position);
    }

    public function getItems(): array
    {
        $items = $this->manualItems + $this->overrideItems;

        ksort($items);

        return $items;
    }

    public function hasManualItem($position): bool
    {
        return isset($this->manualItems[$position]);
    }

    public function getManualItem($position): ?APIItem
    {
        return $this->manualItems[$position] ?? null;
    }

    public function getManualItems(): array
    {
        return $this->manualItems;
    }

    public function hasOverrideItem($position): bool
    {
        return isset($this->overrideItems[$position]);
    }

    public function getOverrideItem($position): ?APIItem
    {
        return $this->overrideItems[$position] ?? null;
    }

    public function getOverrideItems(): array
    {
        return $this->overrideItems;
    }

    public function getQuery(): APIQuery
    {
        return $this->query;
    }

    public function hasQuery(): bool
    {
        return $this->query instanceof APIQuery;
    }

    public function getAvailableLocales(): array
    {
    }

    public function getMainLocale(): string
    {
    }

    public function isTranslatable(): bool
    {
    }

    public function isAlwaysAvailable(): bool
    {
    }

    public function getLocale(): string
    {
    }
}
