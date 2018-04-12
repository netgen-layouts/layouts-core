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
     * Loads all published layouts. If $includeDrafts is set to true, drafts which have no
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
     * Loads all published shared layouts. If $includeDrafts is set to true, drafts which have no
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
     * Loads all published layouts related to provided shared layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $sharedLayout
     * @param int $offset
     * @param int $limit
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided layout is not shared
     *                                                          If provided layout is not published
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout[]
     */
    public function loadRelatedLayouts(Layout $sharedLayout, $offset = 0, $limit = null);

    /**
     * Returns the count of published layouts related to provided shared layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $sharedLayout
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided layout is not shared
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
     * If $excludedLayoutId is provided, the check will not apply to the provided ID.
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
     * Removes the existing zone link from the provided zone.
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
     * Adds a translation with provided locale to the layout.
     *
     * Data for the new translation will be copied from the translation with provided $sourceLocale.
     *
     * All blocks and their collections and queries in the layout will receive the newly added
     * translation, except those that are marked as untranslatable.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param string $locale
     * @param string $sourceLocale
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not a draft
     *                                                          If translation with provided locale already exists
     *                                                          If translation with provided source locale does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function addTranslation(Layout $layout, $locale, $sourceLocale);

    /**
     * Sets the translation with provided locale to be the main one of the provided layout.
     *
     * Setting the main translation will propagate to all the blocks and their collections and
     * queries.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param string $mainLocale
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not a draft
     *                                                          If translation with provided locale does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function setMainTranslation(Layout $layout, $mainLocale);

    /**
     * Removes the translation with provided locale from the layout.
     *
     * Translation will be removed from all blocks and their collections and queries too. If the
     * translation being removed is the only one for a block, the block will be removed too.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param string $locale
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not a draft
     *                                                          If translation with provided locale does not exist
     *                                                          If translation with provided locale is the main layout translation
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function removeTranslation(Layout $layout, $locale);

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
     * Changes the provided layout type.
     *
     * Zone mappings are multidimensional array where keys on the first level are
     * identifiers of the zones in the new layout type, while the values are the list
     * of old zones which will be mapped to the new one. e.g.
     *
     * array(
     *     'left' => array('left', 'right'),
     *     'top' => array('top'),
     * )
     *
     * If $preserveSharedZones is set to true, all zones which are linked to a shared zone
     * will still be preserved as shared after mapping. Due to how shared zones work, if one
     * of the zones from the new layout type is mapped to a shared zone from the old layout,
     * the mapping needs to be 1:1, instead of 1:n.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param \Netgen\BlockManager\Layout\Type\LayoutType $targetLayoutType
     * @param array $zoneMappings
     * @param bool $preserveSharedZones
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If layout is not a draft
     *                                                          If layout is already of provided target type
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function changeLayoutType(Layout $layout, LayoutType $targetLayoutType, array $zoneMappings = array(), $preserveSharedZones = true);

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
     * Restores the archived version of a layout to a draft. If draft already exists,
     * it will be removed.
     *
     * @param int|string $layoutId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with provided ID does not have an archived version
     *                                                          If layout with provided ID does not have a published version to restore the name from
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function restoreFromArchive($layoutId);

    /**
     * Deletes a specified layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     */
    public function deleteLayout(Layout $layout);

    /**
     * Creates a new layout create struct from the provided values.
     *
     * @param \Netgen\BlockManager\Layout\Type\LayoutType $layoutType
     * @param string $name
     * @param string $mainLocale
     *
     * @return \Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct
     */
    public function newLayoutCreateStruct(LayoutType $layoutType, $name, $mainLocale);

    /**
     * Creates a new layout update struct.
     *
     * If the layout is provided, initial data is copied from the layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @return \Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct
     */
    public function newLayoutUpdateStruct(Layout $layout = null);

    /**
     * Creates a new layout copy struct.
     *
     * If the layout is provided, initial data is copied from the layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @return \Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct
     */
    public function newLayoutCopyStruct(Layout $layout = null);
}
