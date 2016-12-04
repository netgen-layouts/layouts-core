<?php

namespace Netgen\BlockManager\API\Service;

use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\API\Values\Page\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Page\LayoutUpdateStruct;
use Netgen\BlockManager\API\Values\Page\Zone;
use Netgen\BlockManager\Configuration\LayoutType\LayoutType;

interface LayoutService
{
    /**
     * Loads a layout with specified ID.
     *
     * @param int|string $layoutId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function loadLayout($layoutId);

    /**
     * Loads a layout draft with specified ID.
     *
     * @param int|string $layoutId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function loadLayoutDraft($layoutId);

    /**
     * Loads all layouts. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     *
     * @param bool $includeDrafts
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout[]
     */
    public function loadLayouts($includeDrafts = false, $offset = 0, $limit = null);

    /**
     * Loads all shared layouts. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     *
     * @param bool $includeDrafts
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout[]
     */
    public function loadSharedLayouts($includeDrafts = false, $offset = 0, $limit = null);

    /**
     * Returns if provided layout has a published status.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @return bool
     */
    public function hasPublishedState(Layout $layout);

    /**
     * Loads a zone with specified identifier.
     *
     * @param int|string $layoutId
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with specified ID or zone with specified identifier do not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function loadZone($layoutId, $identifier);

    /**
     * Loads a zone draft with specified identifier.
     *
     * @param int|string $layoutId
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with specified ID or zone with specified identifier do not exist
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function loadZoneDraft($layoutId, $identifier);

    /**
     * Returns if layout with provided name exists.
     *
     * @param string $name
     * @param int|string $excludedLayoutId
     *
     * @return bool
     */
    public function layoutNameExists($name, $excludedLayoutId = null);

    /**
     * Links the zone to provided linked zone. If zone had a previous link, it will be overwritten.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     * @param \Netgen\BlockManager\API\Values\Page\Zone $linkedZone
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone is not a draft
     * @throws \Netgen\BlockManager\Exception\BadStateException If linked zone is not published
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone is in the shared layout
     * @throws \Netgen\BlockManager\Exception\BadStateException If linked zone is not in the shared layout
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone and linked zone belong to the same layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function linkZone(Zone $zone, Zone $linkedZone);

    /**
     * Removes the link in the zone.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone
     */
    public function unlinkZone(Zone $zone);

    /**
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\LayoutCreateStruct $layoutCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function createLayout(LayoutCreateStruct $layoutCreateStruct);

    /**
     * Updates a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param \Netgen\BlockManager\API\Values\Page\LayoutUpdateStruct $layoutUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not a draft
     *                                                          If layout with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function updateLayout(Layout $layout, LayoutUpdateStruct $layoutUpdateStruct);

    /**
     * Copies a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     * @param string $newName
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function copyLayout(Layout $layout, $newName);

    /**
     * Creates a layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not published
     *                                                          If draft already exists for layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function createDraft(Layout $layout);

    /**
     * Discards a layout draft.
     *
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not a draft
     */
    public function discardDraft(Layout $layout);

    /**
     * Publishes a layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function publishLayout(Layout $layout);

    /**
     * Deletes a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     */
    public function deleteLayout(Layout $layout);

    /**
     * Creates a new layout create struct.
     *
     * @param \Netgen\BlockManager\Configuration\LayoutType\LayoutType $layoutType
     * @param string $name
     *
     * @return \Netgen\BlockManager\API\Values\Page\LayoutCreateStruct
     */
    public function newLayoutCreateStruct(LayoutType $layoutType, $name);

    /**
     * Creates a new layout update struct.
     *
     * @return \Netgen\BlockManager\API\Values\Page\LayoutUpdateStruct
     */
    public function newLayoutUpdateStruct();
}
