<?php

namespace Netgen\BlockManager\API\Service;

use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\LayoutUpdateStruct;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\API\Values\Page\LayoutDraft;
use Netgen\BlockManager\API\Values\Page\Zone;
use Netgen\BlockManager\API\Values\Page\ZoneDraft;

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
     * @return \Netgen\BlockManager\API\Values\Page\LayoutDraft
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
    public function isPublished(Layout $layout);

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
     * @return \Netgen\BlockManager\API\Values\Page\ZoneDraft
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
     * @param \Netgen\BlockManager\API\Values\Page\ZoneDraft $zone
     * @param \Netgen\BlockManager\API\Values\Page\Zone $linkedZone
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone is in the shared layout
     * @throws \Netgen\BlockManager\Exception\BadStateException If linked zone is not in the shared layout
     * @throws \Netgen\BlockManager\Exception\BadStateException If zone and linked zone belong to the same layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\ZoneDraft
     */
    public function linkZone(ZoneDraft $zone, Zone $linkedZone);

    /**
     * Removes the link in the zone.
     *
     * @param \Netgen\BlockManager\API\Values\Page\ZoneDraft $zone
     *
     * @return \Netgen\BlockManager\API\Values\Page\ZoneDraft
     */
    public function unlinkZone(ZoneDraft $zone);

    /**
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutCreateStruct $layoutCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If layout type does not exist
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Page\LayoutDraft
     */
    public function createLayout(LayoutCreateStruct $layoutCreateStruct);

    /**
     * Updates a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\LayoutDraft $layout
     * @param \Netgen\BlockManager\API\Values\LayoutUpdateStruct $layoutUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout with provided name already exists
     *
     * @return \Netgen\BlockManager\API\Values\Page\LayoutDraft
     */
    public function updateLayout(LayoutDraft $layout, LayoutUpdateStruct $layoutUpdateStruct);

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
     * @throws \Netgen\BlockManager\Exception\BadStateException If draft already exists for layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\LayoutDraft
     */
    public function createDraft(Layout $layout);

    /**
     * Discards a layout draft.
     *
     * @param \Netgen\BlockManager\API\Values\Page\LayoutDraft $layout
     */
    public function discardDraft(LayoutDraft $layout);

    /**
     * Publishes a layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\LayoutDraft $layout
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function publishLayout(LayoutDraft $layout);

    /**
     * Deletes a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     */
    public function deleteLayout(Layout $layout);

    /**
     * Creates a new layout create struct.
     *
     * @param string $type
     * @param string $name
     *
     * @return \Netgen\BlockManager\API\Values\LayoutCreateStruct
     */
    public function newLayoutCreateStruct($type, $name);

    /**
     * Creates a new layout update struct.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutUpdateStruct
     */
    public function newLayoutUpdateStruct();
}
