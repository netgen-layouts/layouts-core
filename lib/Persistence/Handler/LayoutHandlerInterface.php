<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Handler;

use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\Zone;
use Netgen\BlockManager\Persistence\Values\Layout\ZoneCreateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\ZoneUpdateStruct;

interface LayoutHandlerInterface
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
    public function loadLayout($layoutId, int $status): Layout;

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
    public function loadZone($layoutId, int $status, string $identifier): Zone;

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
    public function loadLayouts(bool $includeDrafts = false, int $offset = 0, int $limit = null): array;

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
    public function loadSharedLayouts(bool $includeDrafts = false, int $offset = 0, int $limit = null): array;

    /**
     * Loads all layouts related to provided shared layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $sharedLayout
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout[]
     */
    public function loadRelatedLayouts(Layout $sharedLayout, int $offset = 0, int $limit = null): array;

    /**
     * Loads the count of layouts related to provided shared layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $sharedLayout
     *
     * @return int
     */
    public function getRelatedLayoutsCount(Layout $sharedLayout): int;

    /**
     * Returns if layout with specified ID exists.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @return bool
     */
    public function layoutExists($layoutId, int $status): bool;

    /**
     * Returns if zone with specified identifier exists in the layout.
     *
     * @param int|string $layoutId
     * @param int $status
     * @param string $identifier
     *
     * @return bool
     */
    public function zoneExists($layoutId, int $status, string $identifier): bool;

    /**
     * Loads all zones that belong to layout with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Zone[]
     */
    public function loadLayoutZones(Layout $layout): array;

    /**
     * Returns if layout with provided name exists.
     *
     * @param string $name
     * @param int|string $excludedLayoutId
     *
     * @return bool
     */
    public function layoutNameExists(string $name, $excludedLayoutId = null): bool;

    /**
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\LayoutCreateStruct $layoutCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout
     */
    public function createLayout(LayoutCreateStruct $layoutCreateStruct): Layout;

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
    public function createLayoutTranslation(Layout $layout, string $locale, string $sourceLocale): Layout;

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
    public function setMainTranslation(Layout $layout, string $mainLocale): Layout;

    /**
     * Creates a zone in provided layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     * @param \Netgen\BlockManager\Persistence\Values\Layout\ZoneCreateStruct $zoneCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Zone
     */
    public function createZone(Layout $layout, ZoneCreateStruct $zoneCreateStruct): Zone;

    /**
     * Updates a layout with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     * @param \Netgen\BlockManager\Persistence\Values\Layout\LayoutUpdateStruct $layoutUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout
     */
    public function updateLayout(Layout $layout, LayoutUpdateStruct $layoutUpdateStruct): Layout;

    /**
     * Updates a specified zone.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Zone $zone
     * @param \Netgen\BlockManager\Persistence\Values\Layout\ZoneUpdateStruct $zoneUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Zone
     */
    public function updateZone(Zone $zone, ZoneUpdateStruct $zoneUpdateStruct): Zone;

    /**
     * Copies the layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     * @param \Netgen\BlockManager\Persistence\Values\Layout\LayoutCopyStruct $layoutCopyStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout
     */
    public function copyLayout(Layout $layout, LayoutCopyStruct $layoutCopyStruct): Layout;

    /**
     * Changes the provided layout type.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     * @param string $targetLayoutType
     * @param array $zoneMappings
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout
     */
    public function changeLayoutType(Layout $layout, string $targetLayoutType, array $zoneMappings = []): Layout;

    /**
     * Creates a new layout status.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout $layout
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout
     */
    public function createLayoutStatus(Layout $layout, int $newStatus): Layout;

    /**
     * Deletes a layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     */
    public function deleteLayout($layoutId, int $status = null): void;

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
    public function deleteLayoutTranslation(Layout $layout, string $locale): Layout;
}
