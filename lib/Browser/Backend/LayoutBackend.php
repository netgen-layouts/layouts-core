<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Browser\Backend;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
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

    public function getDefaultSections()
    {
        return [new RootLocation()];
    }

    public function loadLocation($id): LocationInterface
    {
        return new RootLocation();
    }

    public function loadItem($id): ItemInterface
    {
        try {
            $layout = $this->layoutService->loadLayout($id);
        } catch (BaseNotFoundException $e) {
            throw new NotFoundException(
                sprintf('Item with ID %s not found.', $id),
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

        return $this->buildItems($layouts);
    }

    public function getSubItemsCount(LocationInterface $location): int
    {
        $layouts = $this->layoutService->loadLayouts();

        return count($layouts);
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
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout[] $layouts
     *
     * @return \Netgen\BlockManager\Browser\Item\Layout\Item[]
     */
    private function buildItems(array $layouts): array
    {
        return array_map(
            function (Layout $layout): Item {
                return $this->buildItem($layout);
            },
            $layouts
        );
    }
}
