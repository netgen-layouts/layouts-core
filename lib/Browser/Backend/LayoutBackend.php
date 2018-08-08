<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Browser\Backend;

use Generator;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\LayoutList;
use Netgen\BlockManager\Browser\Item\Layout\Item;
use Netgen\BlockManager\Browser\Item\Layout\RootLocation;
use Netgen\BlockManager\Exception\NotFoundException as BaseNotFoundException;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Exceptions\NotFoundException;
use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\LocationInterface;

final class LayoutBackend implements BackendInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    public function getSections()
    {
        return [new RootLocation()];
    }

    public function loadLocation($id): LocationInterface
    {
        return new RootLocation();
    }

    public function loadItem($value): ItemInterface
    {
        try {
            $layout = $this->layoutService->loadLayout($value);
        } catch (BaseNotFoundException $e) {
            throw new NotFoundException(
                sprintf('Item with value "%s" not found.', $value),
                0,
                $e
            );
        }

        return $this->buildItem($layout);
    }

    public function getSubLocations(LocationInterface $location)
    {
        return [];
    }

    public function getSubLocationsCount(LocationInterface $location): int
    {
        return 0;
    }

    public function getSubItems(LocationInterface $location, $offset = 0, $limit = 25)
    {
        $layouts = $this->layoutService->loadLayouts(false, $offset, $limit);

        return iterator_to_array($this->buildItems($layouts));
    }

    public function getSubItemsCount(LocationInterface $location): int
    {
        return $this->layoutService->getLayoutsCount();
    }

    public function search($searchText, $offset = 0, $limit = 25)
    {
        return [];
    }

    public function searchCount($searchText): int
    {
        return 0;
    }

    /**
     * Builds the item from provided layout.
     */
    private function buildItem(Layout $layout): Item
    {
        return new Item($layout);
    }

    /**
     * Builds the items from provided layouts.
     */
    private function buildItems(LayoutList $layouts): Generator
    {
        foreach ($layouts as $layout) {
            yield $this->buildItem($layout);
        }
    }
}
