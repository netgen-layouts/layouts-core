<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Service;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\LayoutCopyStruct;
use Netgen\Layouts\API\Values\Layout\LayoutCreateStruct;
use Netgen\Layouts\API\Values\Layout\LayoutList;
use Netgen\Layouts\API\Values\Layout\LayoutUpdateStruct;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Layout\Type\LayoutTypeInterface;
use Ramsey\Uuid\UuidInterface;

interface LayoutService extends TransactionService
{
    /**
     * Loads a layout with specified UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If layout with specified UUID does not exist
     */
    public function loadLayout(UuidInterface $layoutId): Layout;

    /**
     * Loads a layout draft with specified UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If layout with specified UUID does not exist
     */
    public function loadLayoutDraft(UuidInterface $layoutId): Layout;

    /**
     * Loads a layout archive with specified UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If layout with specified UUID does not exist
     */
    public function loadLayoutArchive(UuidInterface $layoutId): Layout;

    /**
     * Loads all published non-shared layouts. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     */
    public function loadLayouts(bool $includeDrafts = false, int $offset = 0, ?int $limit = null): LayoutList;

    /**
     * Returns the count of all published non-shared layouts. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     */
    public function getLayoutsCount(bool $includeDrafts = false): int;

    /**
     * Loads all published shared layouts. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     */
    public function loadSharedLayouts(bool $includeDrafts = false, int $offset = 0, ?int $limit = null): LayoutList;

    /**
     * Returns the count of all published shared layouts. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     */
    public function getSharedLayoutsCount(bool $includeDrafts = false): int;

    /**
     * Loads all published layouts. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     */
    public function loadAllLayouts(bool $includeDrafts = false, int $offset = 0, ?int $limit = null): LayoutList;

    /**
     * Returns the count of all published layouts. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     */
    public function getAllLayoutsCount(bool $includeDrafts = false): int;

    /**
     * Loads all published layouts related to provided shared layout.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If provided layout is not shared
     *                                                     If provided layout is not published
     */
    public function loadRelatedLayouts(Layout $sharedLayout): LayoutList;

    /**
     * Returns the count of published layouts related to provided shared layout.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If provided layout is not shared
     *                                                     If provided layout is not published
     */
    public function getRelatedLayoutsCount(Layout $sharedLayout): int;

    /**
     * Returns if layout with provided UUID has a provided status (published, draft or archived).
     *
     * @deprecated Will be removed in 2.0. Use LayoutService::layoutExists.
     */
    public function hasStatus(UuidInterface $layoutId, int $status): bool;

    /**
     * Returns if layout with provided UUID, and optionally status, exists.
     */
    public function layoutExists(UuidInterface $layoutId, ?int $status = null): bool;

    /**
     * Returns if layout with provided name exists.
     *
     * If $excludedLayoutId is provided, the check will not apply to the provided UUID.
     */
    public function layoutNameExists(string $name, ?UuidInterface $excludedLayoutId = null): bool;

    /**
     * Links the zone to provided linked zone. If zone had a previous link, it will be overwritten.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If zone is not a draft
     *                                                     If linked zone is not published
     *                                                     If zone is in the shared layout
     *                                                     If linked zone is not in the shared layout
     *                                                     If zone and linked zone belong to the same layout
     */
    public function linkZone(Zone $zone, Zone $linkedZone): void;

    /**
     * Removes the existing zone link from the provided zone.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If zone is not a draft
     */
    public function unlinkZone(Zone $zone): void;

    /**
     * Creates a layout.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If layout with provided name already exists
     */
    public function createLayout(LayoutCreateStruct $layoutCreateStruct): Layout;

    /**
     * Adds a translation with provided locale to the layout.
     *
     * Data for the new translation will be copied from the translation with provided $sourceLocale.
     *
     * All blocks and their collections and queries in the layout will receive the newly added
     * translation, except those that are marked as untranslatable.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If layout is not a draft
     *                                                     If translation with provided locale already exists
     *                                                     If translation with provided source locale does not exist
     */
    public function addTranslation(Layout $layout, string $locale, string $sourceLocale): Layout;

    /**
     * Sets the translation with provided locale to be the main one of the provided layout.
     *
     * Setting the main translation will propagate to all the blocks and their collections and
     * queries.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If layout is not a draft
     *                                                     If translation with provided locale does not exist
     */
    public function setMainTranslation(Layout $layout, string $mainLocale): Layout;

    /**
     * Removes the translation with provided locale from the layout.
     *
     * Translation will be removed from all blocks and their collections and queries too. If the
     * translation being removed is the only one for a block, the block will be removed too.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If layout is not a draft
     *                                                     If translation with provided locale does not exist
     *                                                     If translation with provided locale is the main layout translation
     */
    public function removeTranslation(Layout $layout, string $locale): Layout;

    /**
     * Updates a specified layout.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If layout is not a draft
     *                                                     If layout with provided name already exists
     */
    public function updateLayout(Layout $layout, LayoutUpdateStruct $layoutUpdateStruct): Layout;

    /**
     * Copies a specified layout.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If layout with provided name already exists
     */
    public function copyLayout(Layout $layout, LayoutCopyStruct $layoutCopyStruct): Layout;

    /**
     * Changes the provided layout type.
     *
     * Zone mappings are multidimensional array where keys on the first level are
     * identifiers of the zones in the new layout type, while the values are the list
     * of old zones which will be mapped to the new one. e.g.
     *
     * [
     *     'left' => ['left', 'right'],
     *     'top' => ['top'],
     * ]
     *
     * If $preserveSharedZones is set to true, all zones which are linked to a shared zone
     * will still be preserved as shared after mapping. Due to how shared zones work, if one
     * of the zones from the new layout type is mapped to a shared zone from the old layout,
     * the mapping needs to be 1:1, instead of 1:n.
     *
     * @param array<string, string[]> $zoneMappings
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If layout is not a draft
     */
    public function changeLayoutType(Layout $layout, LayoutTypeInterface $targetLayoutType, array $zoneMappings, bool $preserveSharedZones = true): Layout;

    /**
     * Creates a layout draft.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If layout is not published
     *                                                     If draft already exists for layout and $discardExisting is set to false
     */
    public function createDraft(Layout $layout, bool $discardExisting = false): Layout;

    /**
     * Discards a layout draft.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If layout is not a draft
     */
    public function discardDraft(Layout $layout): void;

    /**
     * Publishes a layout.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If layout is not a draft
     */
    public function publishLayout(Layout $layout): Layout;

    /**
     * Restores the archived version of a layout to a draft. If draft already exists,
     * it will be removed.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If provided layout is not in archived status
     * @throws \Netgen\Layouts\Exception\NotFoundException If provided layout does not have a published version to restore the name from
     */
    public function restoreFromArchive(Layout $layout): Layout;

    /**
     * Deletes a specified layout.
     */
    public function deleteLayout(Layout $layout): void;

    /**
     * Creates a new layout create struct from the provided values.
     */
    public function newLayoutCreateStruct(LayoutTypeInterface $layoutType, string $name, string $mainLocale): LayoutCreateStruct;

    /**
     * Creates a new layout update struct.
     *
     * If the layout is provided, initial data is copied from the layout.
     */
    public function newLayoutUpdateStruct(?Layout $layout = null): LayoutUpdateStruct;

    /**
     * Creates a new layout copy struct.
     *
     * If the layout is provided, initial data is copied from the layout.
     */
    public function newLayoutCopyStruct(?Layout $layout = null): LayoutCopyStruct;
}
