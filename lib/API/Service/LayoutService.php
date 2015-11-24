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
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If layout ID has an invalid or empty value
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function loadLayout($layoutId);

    /**
     * Loads a layout with specified identifier.
     *
     * @param string $layoutIdentifier
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If layout identifier has an invalid or empty value
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If layout with specified identifier does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function loadLayoutByIdentifier($layoutIdentifier);

    /**
     * Loads a zone with specified ID.
     *
     * @param int|string $zoneId
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If zone ID has an invalid or empty value
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If zone with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function loadZone($zoneId);

    /**
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutCreateStruct $layoutCreateStruct
     * @param \Netgen\BlockManager\API\Values\Page\Layout $parentLayout
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If create struct properties have an invalid or empty value
     *                                                                  If layout with same identifier already exists
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function createLayout(LayoutCreateStruct $layoutCreateStruct, Layout $parentLayout = null);

    /**
     * Copies a specified layout. If layout identifier is provided, the layout will
     * have that identifier set. Otherwise, the new layout will have a "copy_of_<oldLayoutIdentifier>"
     * identifier.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param string $newLayoutIdentifier
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If layout with provided identifier already exists
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function copyLayout(Layout $layout, $newLayoutIdentifier = null);

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
     * @param string[] $zoneIdentifiers
     * @param string $name
     *
     * @return \Netgen\BlockManager\API\Values\LayoutCreateStruct
     */
    public function newLayoutCreateStruct($layoutIdentifier, array $zoneIdentifiers, $name);
}
