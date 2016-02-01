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
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If layout ID has an invalid or empty value
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function loadLayout($layoutId, $status = Layout::STATUS_PUBLISHED);

    /**
     * Loads a zone with specified identifier.
     *
     * @param int|string $layoutId
     * @param string $identifier
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If layout ID or zone identifier have an invalid or empty value
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If layout with specified ID or zone with specified identifier do not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function loadZone($layoutId, $identifier, $status = Layout::STATUS_PUBLISHED);

    /**
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutCreateStruct $layoutCreateStruct
     * @param \Netgen\BlockManager\API\Values\Page\Layout $parentLayout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function createLayout(LayoutCreateStruct $layoutCreateStruct, Layout $parentLayout = null);

    /**
     * Copies a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function copyLayout(Layout $layout);

    /**
     * Creates a new layout status.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function createLayoutStatus(Layout $layout, $newStatus);

    /**
     * Publishes a layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If layout is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function publishLayout(Layout $layout);

    /**
     * Deletes a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param int $status
     */
    public function deleteLayout(Layout $layout, $status = null);

    /**
     * Creates a new layout create struct.
     *
     * @param string $identifier
     * @param string $name
     * @param string[] $zoneIdentifiers
     *
     * @return \Netgen\BlockManager\API\Values\LayoutCreateStruct
     */
    public function newLayoutCreateStruct($identifier, $name, array $zoneIdentifiers);
}
