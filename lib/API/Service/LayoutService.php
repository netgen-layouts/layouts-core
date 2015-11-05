<?php

namespace Netgen\BlockManager\API\Service;

use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Page\Layout;

interface LayoutService
{
    /**
     * Loads a layout with specified ID.
     *
     * @param int|string $layoutId
     *
     * @throws \Netgen\BlockManager\Exceptions\InvalidArgumentException If layout ID has an invalid or empty value
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function loadLayout($layoutId);

    /**
     * Loads a zone with specified ID.
     *
     * @param int|string $zoneId
     *
     * @throws \Netgen\BlockManager\Exceptions\InvalidArgumentException If zone ID has an invalid or empty value
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function loadZone($zoneId);

    /**
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutCreateStruct $layoutCreateStruct
     *
     * @throws \Netgen\BlockManager\Exceptions\InvalidArgumentException If create struct properties have an invalid or empty value
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function createLayout(LayoutCreateStruct $layoutCreateStruct);

    /**
     * Copies a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function copyLayout(Layout $layout);

    /**
     * Deletes a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     */
    public function deleteLayout(Layout $layout);

    /**
     * Creates a new layout create struct.
     *
     * @param string $layoutIdentifier
     * @param int|mixed $parentId
     *
     * @return \Netgen\BlockManager\API\Values\LayoutCreateStruct
     */
    public function newLayoutCreateStruct($layoutIdentifier, $parentId = null);
}
