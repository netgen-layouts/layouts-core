<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\App\Browser;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Backend\SearchQuery;
use Netgen\ContentBrowser\Backend\SearchResult;
use Netgen\ContentBrowser\Backend\SearchResultInterface;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;
use Netgen\Layouts\Exception\RuntimeException;

final class MyValueTypeBackend implements BackendInterface
{
    public function getSections(): iterable
    {
        throw new RuntimeException('Not implemented');
    }

    public function loadLocation($id): LocationInterface
    {
        throw new RuntimeException('Not implemented');
    }

    public function loadItem($value): ItemInterface
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

    public function search(string $searchText, int $offset = 0, int $limit = 25): iterable
    {
        return [];
    }

    public function searchCount(string $searchText): int
    {
        return 0;
    }
}
