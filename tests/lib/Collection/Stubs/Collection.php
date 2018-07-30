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
    private $items = [];

    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Query|null
     */
    private $query;

    public function __construct(
        array $items = [],
        ?array $queryValues = null,
        int $queryCount = 0
    ) {
        foreach ($items as $position => $value) {
            $this->items[$position] = Item::fromArray(
                [
                    'value' => $value,
                    'cmsItem' => $value !== null ?
                        CmsItem::fromArray(['value' => $value, 'isVisible' => true]) :
                        new NullCmsItem('value'),
                    'position' => $position,
                ]
            );
        }

        if ($queryValues !== null) {
            $this->query = Query::fromArray(
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

    public function hasItem(int $position): bool
    {
        return isset($this->items[$position]);
    }

    public function getItem(int $position): ?APIItem
    {
        if ($this->hasItem($position)) {
            return $this->items[$position];
        }

        return null;
    }

    public function getItems(): array
    {
        return $this->items;
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
