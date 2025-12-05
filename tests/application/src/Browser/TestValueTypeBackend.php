<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\App\Browser;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Backend\SearchQuery;
use Netgen\ContentBrowser\Backend\SearchResult;
use Netgen\ContentBrowser\Backend\SearchResultInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\Layouts\Exception\RuntimeException;

final class TestValueTypeBackend implements BackendInterface
{
    public function getSections(): never
    {
        throw new RuntimeException('Not implemented');
    }

    public function loadLocation(int|string $id): never
    {
        throw new RuntimeException('Not implemented');
    }

    public function loadItem(int|string $value): never
    {
        throw new RuntimeException('Not implemented');
    }

    public function getSubLocations(LocationInterface $location): iterable
    {
        return [];
    }

    public function getSubLocationsCount(LocationInterface $location): int
    {
        return 0;
    }

    public function getSubItems(LocationInterface $location, int $offset = 0, int $limit = 25): iterable
    {
        return [];
    }

    public function getSubItemsCount(LocationInterface $location): int
    {
        return 0;
    }

    public function searchItems(SearchQuery $searchQuery): SearchResultInterface
    {
        return new SearchResult();
    }

    public function searchItemsCount(SearchQuery $searchQuery): int
    {
        return 0;
    }
}
