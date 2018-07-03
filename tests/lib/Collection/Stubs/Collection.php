<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use Netgen\BlockManager\API\Values\Collection\Collection as APICollection;
use Netgen\BlockManager\API\Values\Collection\Item as APIItem;
use Netgen\BlockManager\API\Values\Collection\Query as APIQuery;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Item\NullCmsItem;

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
     * @var \Netgen\BlockManager\API\Values\Collection\Query|null
     */
    private $query;

    public function __construct(
        array $manualItems = [],
        array $overrideItems = [],
        ?array $queryValues = null,
        int $queryCount = 0
    ) {
        foreach ($manualItems as $position => $value) {
            $this->manualItems[$position] = new Item(
                [
                    'type' => Item::TYPE_MANUAL,
                    'value' => $value,
                    'cmsItem' => $value !== null ?
                        new CmsItem(['value' => $value, 'isVisible' => true]) :
                        new NullCmsItem('value'),
                    'position' => $position,
                ]
            );
        }

        foreach ($overrideItems as $position => $value) {
            $this->overrideItems[$position] = new Item(
                [
                    'type' => Item::TYPE_OVERRIDE,
                    'value' => $value,
                    'cmsItem' => $value !== null ?
                        new CmsItem(['value' => $value, 'isVisible' => true]) :
                        new NullCmsItem('value'),
                    'position' => $position,
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
        return null;
    }

    public function getStatus(): int
    {
        return self::STATUS_DRAFT;
    }

    public function getType(): int
    {
        return self::TYPE_MANUAL;
    }

    public function isDraft(): bool
    {
        return true;
    }

    public function isPublished(): bool
    {
        return false;
    }

    public function isArchived(): bool
    {
        return false;
    }

    public function getOffset(): int
    {
        return 0;
    }

    public function getLimit(): ?int
    {
        return null;
    }

    public function hasItem(int $position, ?int $type = null): bool
    {
        return $this->hasManualItem($position) || $this->hasOverrideItem($position);
    }

    public function getItem(int $position, ?int $type = null): ?APIItem
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

    public function hasManualItem(int $position): bool
    {
        return isset($this->manualItems[$position]);
    }

    public function getManualItem(int $position): ?APIItem
    {
        return $this->manualItems[$position] ?? null;
    }

    public function getManualItems(): array
    {
        return $this->manualItems;
    }

    public function hasOverrideItem(int $position): bool
    {
        return isset($this->overrideItems[$position]);
    }

    public function getOverrideItem(int $position): ?APIItem
    {
        return $this->overrideItems[$position] ?? null;
    }

    public function getOverrideItems(): array
    {
        return $this->overrideItems;
    }

    public function getQuery(): ?APIQuery
    {
        return $this->query;
    }

    public function hasQuery(): bool
    {
        return $this->query instanceof APIQuery;
    }

    public function getAvailableLocales(): array
    {
        return ['en'];
    }

    public function getMainLocale(): string
    {
        return 'en';
    }

    public function isTranslatable(): bool
    {
        return false;
    }

    public function isAlwaysAvailable(): bool
    {
        return true;
    }

    public function getLocale(): string
    {
        return 'en';
    }
}
