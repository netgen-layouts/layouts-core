<?php

namespace Netgen\Bundle\BlockManagerBundle\Browser\Backend;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Page\LayoutInfo;
use Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout\Item;
use Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout\RootLocation;
use Netgen\Bundle\ContentBrowserBundle\Backend\BackendInterface;
use Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface;

class LayoutBackend implements BackendInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     */
    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Returns the default sections available in the backend.
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface[]
     */
    public function getDefaultSections()
    {
        return array(new RootLocation());
    }

    /**
     * Loads a  location by its ID.
     *
     * @param int|string $id
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException If location does not exist
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface
     */
    public function loadLocation($id)
    {
        return new RootLocation();
    }

    /**
     * Loads the item by its value ID.
     *
     * @param int|string $id
     *
     * @throws \Netgen\Bundle\ContentBrowserBundle\Exceptions\NotFoundException If item does not exist
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    public function loadItem($id)
    {
        $layout = $this->layoutService->loadLayoutInfo($id);

        return $this->buildItem($layout);
    }

    /**
     * Returns the locations below provided location.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface $location
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface[]
     */
    public function getSubLocations(LocationInterface $location)
    {
        return array();
    }

    /**
     * Returns the count of locations below provided location.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface $location
     *
     * @return int
     */
    public function getSubLocationsCount(LocationInterface $location)
    {
        return 0;
    }

    /**
     * Returns the location items.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface $location
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[]
     */
    public function getSubItems(LocationInterface $location, $offset = 0, $limit = 25)
    {
        $layouts = $this->layoutService->loadLayouts($offset, $limit);

        return $this->buildItems($layouts);
    }

    /**
     * Returns the location items count.
     *
     * @param \Netgen\Bundle\ContentBrowserBundle\Item\LocationInterface $location
     *
     * @return int
     */
    public function getSubItemsCount(LocationInterface $location)
    {
        $layouts = $this->layoutService->loadLayouts();

        return count($layouts);
    }

    /**
     * Searches for items.
     *
     * @param string $searchText
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface[]
     */
    public function search($searchText, $offset = 0, $limit = 25)
    {
        return array();
    }

    /**
     * Returns the count of searched items.
     *
     * @param string $searchText
     *
     * @return int
     */
    public function searchCount($searchText)
    {
        return 0;
    }

    /**
     * Builds the item from provided layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\LayoutInfo $layout
     *
     * @return \Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout\Item
     */
    protected function buildItem(LayoutInfo $layout)
    {
        return new Item($layout);
    }

    /**
     * Builds the items from provided layouts.
     *
     * @param \Netgen\BlockManager\API\Values\Page\LayoutInfo[] $layouts
     *
     * @return \Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout\Item[]
     */
    protected function buildItems(array $layouts)
    {
        return array_map(
            function (LayoutInfo $layout) {
                return $this->buildItem($layout);
            },
            $layouts
        );
    }
}
