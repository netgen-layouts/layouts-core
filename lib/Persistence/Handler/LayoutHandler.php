<?php

namespace Netgen\BlockManager\Persistence\Handler;

use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\Zone;
use Netgen\BlockManager\Persistence\Values\Layout\ZoneCreateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\ZoneUpdateStruct;

interface LayoutHandler
{
    /**
     * Loads a layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout
     */
    public function loadLayout($layoutId, $status);

    /**
     * Loads a zone with specified identifier.
     *
     * @param int|string $layoutId
     * @param int $status
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with specified ID or zone with specified identifier do not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Zone
     */
    public function loadZone($layoutId, $status, $identifier);

    /**
     * Loads all layouts. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     *
     * @param bool $includeDrafts
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout[]
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
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout[]
     */
    public function loadSharedLayouts($includeDrafts = false, $offset = 0, $limit = null);

    /**
     * Loads all layouts related to provided shared layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $sharedLayout
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout[]
     */
    public function loadRelatedLayouts(Layout $sharedLayout, $offset = 0, $limit = null);

    /**
     * Loads the count of layouts related to provided shared layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $sharedLayout
     *
     * @return int
     */
    public function getRelatedLayoutsCount(Layout $sharedLayout);

    /**
     * Returns if layout with specified ID exists.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @return bool
     */
    public function layoutExists($layoutId, $status);

    /**
     * Returns if zone with specified identifier exists in the layout.
     *
     * @param int|string $layoutId
     * @param int $status
     * @param string $identifier
     *
     * @return bool
     */
    public function zoneExists($layoutId, $status, $identifier);

    /**
     * Loads all zones that belong to layout with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Zone[]
     */
    public function loadLayoutZones(Layout $layout);

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
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\LayoutCreateStruct $layoutCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout
     */
    public function createLayout(LayoutCreateStruct $layoutCreateStruct);

    /**
     * Creates a layout translation.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     * @param string $locale
     * @param string $sourceLocale
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If translation with provided locale already exists
     *                                                          If translation with provided source locale does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout
     */
    public function createLayoutTranslation(Layout $layout, $locale, $sourceLocale);

    /**
     * Updates the main translation of the layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     * @param string $mainLocale
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided locale does not exist in the layout
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout
     */
    public function setMainTranslation(Layout $layout, $mainLocale);

    /**
     * Creates a zone in provided layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     * @param \Netgen\BlockManager\Persistence\Values\Layout\ZoneCreateStruct $zoneCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Zone
     */
    public function createZone(Layout $layout, ZoneCreateStruct $zoneCreateStruct);

    /**
     * Updates a layout with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     * @param \Netgen\BlockManager\Persistence\Values\Layout\LayoutUpdateStruct $layoutUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout
     */
    public function updateLayout(Layout $layout, LayoutUpdateStruct $layoutUpdateStruct);

    /**
     * Updates a specified zone.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Zone $zone
     * @param \Netgen\BlockManager\Persistence\Values\Layout\ZoneUpdateStruct $zoneUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Zone
     */
    public function updateZone(Zone $zone, ZoneUpdateStruct $zoneUpdateStruct);

    /**
     * Copies the layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     * @param \Netgen\BlockManager\Persistence\Values\Layout\LayoutCopyStruct $layoutCopyStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout
     */
    public function copyLayout(Layout $layout, LayoutCopyStruct $layoutCopyStruct);

    /**
     * Changes the provided layout type.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     * @param string $targetLayoutType
     * @param array $zoneMappings
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout
     */
    public function changeLayoutType(Layout $layout, $targetLayoutType, array $zoneMappings = array());

    /**
     * Creates a new layout status.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout $layout
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout
     */
    public function createLayoutStatus(Layout $layout, $newStatus);

    /**
     * Deletes a layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     */
    public function deleteLayout($layoutId, $status = null);

    /**
     * Deletes provided layout translation.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     * @param string $locale
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If translation with provided locale does not exist
     *                                                          If translation with provided locale is the main layout translation
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout
     */
    public function deleteLayoutTranslation(Layout $layout, $locale);
}
