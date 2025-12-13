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
use Netgen\Layouts\Persistence\Values\Status;
use Symfony\Component\Uid\Uuid;

interface LayoutHandlerInterface
{
    /**
     * Loads a layout with specified ID.
     *
     * Layout ID can be an auto-incremented ID or an UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If layout with specified ID does not exist
     */
    public function loadLayout(int|string|Uuid $layoutId, Status $status): Layout;

    /**
     * Loads a zone with specified identifier.
     *
     * Layout ID can be an auto-incremented ID or an UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If layout with specified ID or zone with specified identifier do not exist
     */
    public function loadZone(int|string|Uuid $layoutId, Status $status, string $identifier): Zone;

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
     *
     * @return int<0, max>
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
     *
     * @return int<0, max>
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
     *
     * @return int<0, max>
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
     *
     * @return int<0, max>
     */
    public function getRelatedLayoutsCount(Layout $sharedLayout): int;

    /**
     * Returns if layout with specified ID exists.
     *
     * Layout ID can be an auto-incremented ID or an UUID.
     */
    public function layoutExists(int|string|Uuid $layoutId, ?Status $status = null): bool;

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
     */
    public function layoutNameExists(string $name, int|string|Uuid|null $excludedLayoutId = null): bool;

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
    public function createLayoutStatus(Layout $layout, Status $newStatus): Layout;

    /**
     * Deletes a layout with specified ID.
     */
    public function deleteLayout(int $layoutId, ?Status $status = null): void;

    /**
     * Deletes provided layout translation.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If translation with provided locale does not exist
     *                                                     If translation with provided locale is the main layout translation
     */
    public function deleteLayoutTranslation(Layout $layout, string $locale): Layout;
}
