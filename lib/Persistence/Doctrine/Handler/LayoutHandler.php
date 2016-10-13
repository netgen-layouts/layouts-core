<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler;
use Netgen\BlockManager\Persistence\Handler\BlockHandler as BaseBlockHandler;
use Netgen\BlockManager\Persistence\Handler\CollectionHandler as BaseCollectionHandler;
use Netgen\BlockManager\Persistence\Handler\LayoutHandler as LayoutHandlerInterface;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper;
use Netgen\BlockManager\API\Values\LayoutCreateStruct as APILayoutCreateStruct;
use Netgen\BlockManager\API\Values\LayoutUpdateStruct as APILayoutUpdateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutUpdateStruct;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Values\Page\Zone;
use Netgen\BlockManager\Persistence\Values\ZoneCreateStruct;

class LayoutHandler implements LayoutHandlerInterface
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler
     */
    protected $queryHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\BlockHandler
     */
    protected $blockHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandler
     */
    protected $collectionHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper
     */
    protected $layoutMapper;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler $queryHandler
     * @param \Netgen\BlockManager\Persistence\Handler\BlockHandler $blockHandler
     * @param \Netgen\BlockManager\Persistence\Handler\CollectionHandler $collectionHandler
     * @param \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper $layoutMapper
     */
    public function __construct(
        LayoutQueryHandler $queryHandler,
        BaseBlockHandler $blockHandler,
        BaseCollectionHandler $collectionHandler,
        LayoutMapper $layoutMapper
    ) {
        $this->queryHandler = $queryHandler;
        $this->blockHandler = $blockHandler;
        $this->collectionHandler = $collectionHandler;
        $this->layoutMapper = $layoutMapper;
    }

    /**
     * Loads a layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function loadLayout($layoutId, $status)
    {
        $data = $this->queryHandler->loadLayoutData($layoutId, $status);

        if (empty($data)) {
            throw new NotFoundException('layout', $layoutId);
        }

        $data = $this->layoutMapper->mapLayouts($data);

        return reset($data);
    }

    /**
     * Loads a zone with specified identifier.
     *
     * @param int|string $layoutId
     * @param int $status
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with specified ID or zone with specified identifier do not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Zone
     */
    public function loadZone($layoutId, $status, $identifier)
    {
        $data = $this->queryHandler->loadZoneData($layoutId, $status, $identifier);

        if (empty($data)) {
            throw new NotFoundException('zone', $identifier);
        }

        $data = $this->layoutMapper->mapZones($data);

        return reset($data);
    }

    /**
     * Loads all layouts. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     *
     * @param bool $includeDrafts
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout[]
     */
    public function loadLayouts($includeDrafts = false, $offset = 0, $limit = null)
    {
        $data = $this->queryHandler->loadLayoutsData($includeDrafts, false, $offset, $limit);

        return $this->layoutMapper->mapLayouts($data);
    }

    /**
     * Loads all shared layouts. If $includeDrafts is set to true, drafts which have no
     * published status will also be included.
     *
     * @param bool $includeDrafts
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout[]
     */
    public function loadSharedLayouts($includeDrafts = false, $offset = 0, $limit = null)
    {
        $data = $this->queryHandler->loadLayoutsData($includeDrafts, true, $offset, $limit);

        return $this->layoutMapper->mapLayouts($data);
    }

    /**
     * Returns if layout with specified ID exists.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @return bool
     */
    public function layoutExists($layoutId, $status)
    {
        return $this->queryHandler->layoutExists($layoutId, $status);
    }

    /**
     * Returns if zone with specified identifier exists in the layout.
     *
     * @param int|string $layoutId
     * @param int $status
     * @param string $identifier
     *
     * @return bool
     */
    public function zoneExists($layoutId, $status, $identifier)
    {
        return $this->queryHandler->zoneExists($layoutId, $status, $identifier);
    }

    /**
     * Loads all zones that belong to layout with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Layout $layout
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Zone[]
     */
    public function loadLayoutZones(Layout $layout)
    {
        return $this->layoutMapper->mapZones(
            $this->queryHandler->loadLayoutZonesData(
                $layout->id,
                $layout->status
            )
        );
    }

    /**
     * Returns if layout with provided name exists.
     *
     * @param string $name
     * @param int|string $excludedLayoutId
     * @param int $status
     *
     * @return bool
     */
    public function layoutNameExists($name, $excludedLayoutId = null, $status = null)
    {
        return $this->queryHandler->layoutNameExists($name, $excludedLayoutId, $status);
    }

    /**
     * Links the zone to provided linked zone. If zone had a previous link, it will be overwritten.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Zone $zone
     * @param \Netgen\BlockManager\Persistence\Values\Page\Zone $linkedZone
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Zone
     */
    public function linkZone(Zone $zone, Zone $linkedZone)
    {
        $this->queryHandler->linkZone(
            $zone->layoutId,
            $zone->identifier,
            $zone->status,
            $linkedZone->layoutId,
            $linkedZone->identifier
        );

        return $this->loadZone(
            $zone->layoutId,
            $zone->status,
            $zone->identifier
        );
    }

    /**
     * Removes the link in the zone.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Zone $zone
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Zone
     */
    public function unlinkZone(Zone $zone)
    {
        $this->queryHandler->unlinkZone($zone->layoutId, $zone->identifier, $zone->status);

        return $this->loadZone(
            $zone->layoutId,
            $zone->status,
            $zone->identifier
        );
    }

    /**
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutCreateStruct $layoutCreateStruct
     * @param int $status
     * @param array $zoneIdentifiers
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function createLayout(APILayoutCreateStruct $layoutCreateStruct, $status, array $zoneIdentifiers = array())
    {
        $zoneCreateStructs = array();
        foreach (array_unique($zoneIdentifiers) as $zoneIdentifier) {
            $zoneCreateStructs[] = new ZoneCreateStruct(
                array(
                    'identifier' => $zoneIdentifier,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                )
            );
        }

        $createdLayoutId = $this->queryHandler->createLayout(
            new LayoutCreateStruct(
                array(
                    'type' => $layoutCreateStruct->type,
                    'name' => trim($layoutCreateStruct->name),
                    'status' => $status,
                    'shared' => $layoutCreateStruct->shared !== null ? $layoutCreateStruct->shared : false,
                    'zoneCreateStructs' => $zoneCreateStructs,
                )
            )
        );

        return $this->loadLayout($createdLayoutId, $status);
    }

    /**
     * Updates a layout with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Layout $layout
     * @param \Netgen\BlockManager\API\Values\LayoutUpdateStruct $layoutUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function updateLayout(Layout $layout, APILayoutUpdateStruct $layoutUpdateStruct)
    {
        $this->queryHandler->updateLayout(
            $layout->id,
            $layout->status,
            new LayoutUpdateStruct(
                array(
                    'name' => $layoutUpdateStruct->name !== null ? trim($layoutUpdateStruct->name) : $layout->name,
                )
            )
        );

        return $this->loadLayout($layout->id, $layout->status);
    }

    /**
     * Updates layout modified timestamp.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Layout $layout
     * @param int $timestamp
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function updateModified(Layout $layout, $timestamp)
    {
        $this->queryHandler->updateModified($layout->id, $layout->status, $timestamp);

        return $this->loadLayout($layout->id, $layout->status);
    }

    /**
     * Copies the layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Layout $layout
     * @param string $newName
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function copyLayout(Layout $layout, $newName)
    {
        $layoutZones = $this->loadLayoutZones($layout);

        $zoneCreateStructs = array();
        foreach ($layoutZones as $layoutZone) {
            $zoneCreateStructs[] = new ZoneCreateStruct(
                array(
                    'identifier' => $layoutZone->identifier,
                    'linkedLayoutId' => $layoutZone->linkedLayoutId,
                    'linkedZoneIdentifier' => $layoutZone->linkedZoneIdentifier,
                )
            );
        }

        $copiedLayoutId = $this->queryHandler->createLayout(
            new LayoutCreateStruct(
                array(
                    'type' => $layout->type,
                    'name' => $newName,
                    'status' => $layout->status,
                    'shared' => $layout->shared,
                    'zoneCreateStructs' => $zoneCreateStructs,
                )
            )
        );

        $copiedLayout = $this->loadLayout($copiedLayoutId, $layout->status);

        foreach ($layoutZones as $layoutZone) {
            $zoneBlocks = $this->blockHandler->loadZoneBlocks($layoutZone);
            foreach ($zoneBlocks as $block) {
                $this->blockHandler->copyBlock(
                    $block,
                    $copiedLayout,
                    $layoutZone->identifier
                );
            }
        }

        return $copiedLayout;
    }

    /**
     * Creates a new layout status.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Layout $layout
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function createLayoutStatus(Layout $layout, $newStatus)
    {
        $layoutZones = $this->loadLayoutZones($layout);

        $zoneCreateStructs = array();
        foreach ($layoutZones as $layoutZone) {
            $zoneCreateStructs[] = new ZoneCreateStruct(
                array(
                    'identifier' => $layoutZone->identifier,
                    'linkedLayoutId' => $layoutZone->linkedLayoutId,
                    'linkedZoneIdentifier' => $layoutZone->linkedZoneIdentifier,
                )
            );
        }

        $this->queryHandler->createLayout(
            new LayoutCreateStruct(
                array(
                    'type' => $layout->type,
                    'name' => $layout->name,
                    'status' => $newStatus,
                    'shared' => $layout->shared,
                    'zoneCreateStructs' => $zoneCreateStructs,
                )
            ),
            $layout->id
        );

        foreach ($layoutZones as $layoutZone) {
            $zoneBlocks = $this->blockHandler->loadZoneBlocks($layoutZone);
            foreach ($zoneBlocks as $block) {
                $this->blockHandler->createBlockStatus(
                    $block,
                    $newStatus
                );
            }
        }

        return $this->loadLayout($layout->id, $newStatus);
    }

    /**
     * Deletes a layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     */
    public function deleteLayout($layoutId, $status = null)
    {
        // First delete all non shared collections
        $collectionData = $this->queryHandler->loadLayoutCollectionsData($layoutId, $status);

        foreach ($collectionData as $collectionDataRow) {
            $this->blockHandler->deleteCollectionReference(
                $collectionDataRow['block_id'],
                $collectionDataRow['block_status'],
                $collectionDataRow['identifier']
            );

            if (!$this->collectionHandler->isSharedCollection($collectionDataRow['collection_id'])) {
                $this->collectionHandler->deleteCollection(
                    $collectionDataRow['collection_id'],
                    $collectionDataRow['collection_status']
                );
            }
        }

        $this->queryHandler->deleteLayoutBlocks($layoutId, $status);
        $this->queryHandler->deleteLayout($layoutId, $status);
    }
}
