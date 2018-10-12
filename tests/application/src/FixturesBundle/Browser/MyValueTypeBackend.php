<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Bundle\FixturesBundle\Browser;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;

final class MyValueTypeBackend implements BackendInterface
{
    public function getSections(): iterable
    {
    }

    public function loadLocation($id): LocationInterface
    {
    }

    public function loadItem($value): ItemInterface
    {
    }

    public function getSubLocations(LocationInterface $location): iterable
    {
    }

    public function getSubLocationsCount(LocationInterface $location): int
    {
    }

    public function getSubItems(LocationInterface $location, int $offset = 0, int $limit = 25): iterable
    {
    }

    public function getSubItemsCount(LocationInterface $location): int
    {
    }

    public function search(string $searchText, int $offset = 0, int $limit = 25): iterable
    {
    }

    public function searchCount(string $searchText): int
    {
    }
}
