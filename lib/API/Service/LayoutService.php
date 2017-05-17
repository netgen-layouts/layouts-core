<?php

namespace Netgen\BlockManager\API\Service;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\Layout\Type\LayoutType;

interface LayoutService extends Service
{
    /**
     * Loads a layout with specified ID.
     *
     * @param int|string $layoutId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function loadLayout($layoutId);

    /**
     * Loads a layout draft with specified ID.
     *
     * @param int|string $layoutId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
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
     * @return \Netgen\BlockManager\API\Values\Layout\Layout[]
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
     * @return \Netgen\BlockManager\API\Values\Layout\Layout[]
     */
    public function loadSharedLayouts($includeDrafts = false, $offset = 0, $limit = null);

    /**
     * Loads all layouts related to provided shared layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $sharedLayout
     * @param int $offset
     * @param int $limit
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If provided layout is not shared
     *                                                          If provided layout is not published
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout[]
     */
    public function loadRelatedLayouts(Layout $sharedLayout, $offset = 0, $limit = null);

    /**
     * Loads the count of layouts related to provided shared layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $sharedLayout
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If provided layout is not shared
     *                                                          If provided layout is not published
     *
     * @return int
     */
    public function getRelatedLayoutsCount(Layout $sharedLayout);

    /**
     * Returns if provided layout has a published status.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
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
     * @return \Netgen\BlockManager\API\Values\Layout\Zone
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
     * @return \Netgen\BlockManager\API\Values\Layout\Zone
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
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $linkedZone
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone is not a draft
     * @throws \Netgen\BlockManager\Exception\BadStateException If linked zone is not published
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone is in the shared layout
     * @throws \Netgen\BlockManager\Exception\BadStateException If linked zone is not in the shared layout
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone and linked zone belong to the same layout
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Zone
     */
    public function linkZone(Zone $zone, Zone $linkedZone);

    /**
     * Removes the link in the zone.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Zone
     */
    public function unlinkZone(Zone $zone);

    /**
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct $layoutCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function createLayout(LayoutCreateStruct $layoutCreateStruct);

    /**
     * Updates a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param \Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct $layoutUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not a draft
     *                                                          If layout with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function updateLayout(Layout $layout, LayoutUpdateStruct $layoutUpdateStruct);

    /**
     * Copies a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param \Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct $layoutCopyStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function copyLayout(Layout $layout, LayoutCopyStruct $layoutCopyStruct);

    /**
     * Creates a layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param bool $discardExisting
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not published
     *                                                          If draft already exists for layout and $discardExisting is set to false
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function createDraft(Layout $layout, $discardExisting = false);

    /**
     * Discards a layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not a draft
     */
    public function discardDraft(Layout $layout);

    /**
     * Publishes a layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function publishLayout(Layout $layout);

    /**
     * Deletes a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     */
    public function deleteLayout(Layout $layout);

    /**
     * Creates a new layout create struct.
     *
     * @param \Netgen\BlockManager\Layout\Type\LayoutType $layoutType
     * @param string $name
     *
     * @return \Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct
     */
    public function newLayoutCreateStruct(LayoutType $layoutType, $name);

    /**
     * Creates a new layout update struct.
     *
     * @return \Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct
     */
    public function newLayoutUpdateStruct();
}
