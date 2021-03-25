<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Handler;

use Netgen\Layouts\Persistence\Values\Layout\Layout;
use Netgen\Layouts\Persistence\Values\Layout\LayoutCopyStruct;
use Netgen\Layouts\Persistence\Values\Layout\LayoutCreateStruct;
use Netgen\Layouts\Persistence\Values\Layout\LayoutUpdateStruct;
use Netgen\Layouts\Persistence\Values\Layout\Zone;
use Netgen\Layouts\Persistence\Values\Layout\ZoneCreateStruct;
use Netgen\Layouts\Persistence\Values\Layout\ZoneUpdateStruct;

interface LayoutHandlerInterface
{
    /**
     * Loads a layout with specified ID.
     *
     * Layout ID can be an auto-incremented ID or an UUID.
     *
     * @param int|string|\Ramsey\Uuid\UuidInterface $layoutId
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If layout with specified ID does not exist
     */
    public function loadLayout($layoutId, int $status): Layout;

    /**
     * Loads a zone with specified identifier.
     *
     * Layout ID can be an auto-incremented ID or an UUID.
     *
     * @param int|string|\Ramsey\Uuid\UuidInterface $layoutId
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If layout with specified ID or zone with specified identifier do not exist
     */
    public function loadZone($layoutId, int $status, string $identifier): Zone;

    /**
     * Loads all non-shared layouts. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     *
     * @return \Netgen\Layouts\Persistence\Values\Layout\Layout[]
     */
    public function loadLayouts(bool $includeDrafts = false, int $offset = 0, ?int $limit = null): array;

    /**
     * Returns the count of all non-shared layouts. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     */
    public function getLayoutsCount(bool $includeDrafts = false): int;

    /**
     * Loads all shared layouts. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     *
     * @return \Netgen\Layouts\Persistence\Values\Layout\Layout[]
     */
    public function loadSharedLayouts(bool $includeDrafts = false, int $offset = 0, ?int $limit = null): array;

    /**
     * Returns the count of all shared layouts. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     */
    public function getSharedLayoutsCount(bool $includeDrafts = false): int;

    /**
     * Loads all layouts. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     *
     * @return \Netgen\Layouts\Persistence\Values\Layout\Layout[]
     */
    public function loadAllLayouts(bool $includeDrafts = false, int $offset = 0, ?int $limit = null): array;

    /**
     * Returns the count of all layouts. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     */
    public function getAllLayoutsCount(bool $includeDrafts = false): int;

    /**
     * Loads all layouts related to provided shared layout.
     *
     * @return \Netgen\Layouts\Persistence\Values\Layout\Layout[]
     */
    public function loadRelatedLayouts(Layout $sharedLayout): array;

    /**
     * Loads the count of layouts related to provided shared layout.
     */
    public function getRelatedLayoutsCount(Layout $sharedLayout): int;

    /**
     * Returns if layout with specified ID exists.
     *
     * Layout ID can be an auto-incremented ID or an UUID.
     *
     * @param int|string|\Ramsey\Uuid\UuidInterface $layoutId
     */
    public function layoutExists($layoutId, ?int $status = null): bool;

    /**
     * Loads all zones that belong to provided layout.
     *
     * @return \Netgen\Layouts\Persistence\Values\Layout\Zone[]
     */
    public function loadLayoutZones(Layout $layout): array;

    /**
     * Returns if layout with provided name exists.
     *
     * Excluded layout ID can be an auto-incremented ID or an UUID.
     *
     * @param int|string|\Ramsey\Uuid\UuidInterface|null $excludedLayoutId
     */
    public function layoutNameExists(string $name, $excludedLayoutId = null): bool;

    /**
     * Creates a layout.
     */
    public function createLayout(LayoutCreateStruct $layoutCreateStruct): Layout;

    /**
     * Creates a layout translation.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If translation with provided locale already exists
     *                                                     If translation with provided source locale does not exist
     */
    public function createLayoutTranslation(Layout $layout, string $locale, string $sourceLocale): Layout;

    /**
     * Updates the main translation of the layout.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If provided locale does not exist in the layout
     */
    public function setMainTranslation(Layout $layout, string $mainLocale): Layout;

    /**
     * Creates a zone in provided layout.
     */
    public function createZone(Layout $layout, ZoneCreateStruct $zoneCreateStruct): Zone;

    /**
     * Updates a specified layout.
     */
    public function updateLayout(Layout $layout, LayoutUpdateStruct $layoutUpdateStruct): Layout;

    /**
     * Updates a specified zone.
     */
    public function updateZone(Zone $zone, ZoneUpdateStruct $zoneUpdateStruct): Zone;

    /**
     * Copies the layout.
     */
    public function copyLayout(Layout $layout, LayoutCopyStruct $layoutCopyStruct): Layout;

    /**
     * Changes the provided layout type.
     *
     * @param array<string, string[]> $zoneMappings
     */
    public function changeLayoutType(Layout $layout, string $targetLayoutType, array $zoneMappings): Layout;

    /**
     * Creates a new layout status.
     */
    public function createLayoutStatus(Layout $layout, int $newStatus): Layout;

    /**
     * Deletes a layout with specified ID.
     */
    public function deleteLayout(int $layoutId, ?int $status = null): void;

    /**
     * Deletes provided layout translation.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If translation with provided locale does not exist
     *                                                     If translation with provided locale is the main layout translation
     */
    public function deleteLayoutTranslation(Layout $layout, string $locale): Layout;
}
