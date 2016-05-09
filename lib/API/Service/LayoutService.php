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
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If layout already has the provided status
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function createLayoutStatus(Layout $layout, $status);

    /**
     * Creates a layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If layout is not published
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function createDraft(Layout $layout);

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
     * If $deleteAll is set to true, layout is completely deleted (i.e. all statuses).
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param bool $deleteAll
     */
    public function deleteLayout(Layout $layout, $deleteAll = false);

    /**
     * Creates a new layout create struct.
     *
     * @param string $type
     * @param string $name
     * @param string[] $zoneIdentifiers
     *
     * @return \Netgen\BlockManager\API\Values\LayoutCreateStruct
     */
    public function newLayoutCreateStruct($type, $name, array $zoneIdentifiers);
}
