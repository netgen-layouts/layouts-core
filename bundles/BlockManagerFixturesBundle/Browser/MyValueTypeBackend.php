<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerFixturesBundle\Browser;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Item\LocationInterface;

final class MyValueTypeBackend implements BackendInterface
{
    public function getDefaultSections()
    {
    }

    public function loadLocation($id)
    {
    }

    public function loadItem($value)
    {
    }

    public function getSubLocations(LocationInterface $location)
    {
    }

    public function getSubLocationsCount(LocationInterface $location)
    {
    }

    public function getSubItems(LocationInterface $location, $offset = 0, $limit = 25)
    {
    }

    public function getSubItemsCount(LocationInterface $location)
    {
    }

    public function search($searchText, $offset = 0, $limit = 25)
    {
    }

    public function searchCount($searchText)
    {
    }
}
