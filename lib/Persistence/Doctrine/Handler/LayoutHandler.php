<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler;
use Netgen\BlockManager\Persistence\Handler\BlockHandler as BaseBlockHandler;
use Netgen\BlockManager\Persistence\Handler\LayoutHandler as LayoutHandlerInterface;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper;
use Netgen\BlockManager\Persistence\Values\LayoutCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutUpdateStruct;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Values\Page\Zone;
use Netgen\BlockManager\Persistence\Values\ZoneCreateStruct;
use Netgen\BlockManager\Persistence\Values\ZoneUpdateStruct;

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
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper
     */
    protected $layoutMapper;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler $queryHandler
     * @param \Netgen\BlockManager\Persistence\Handler\BlockHandler $blockHandler
     * @param \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper $layoutMapper
     */
    public function __construct(
        LayoutQueryHandler $queryHandler,
        BaseBlockHandler $blockHandler,
        LayoutMapper $layoutMapper
    ) {
        $this->queryHandler = $queryHandler;
        $this->blockHandler = $blockHandler;
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
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutCreateStruct $layoutCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function createLayout(LayoutCreateStruct $layoutCreateStruct)
    {
        $layoutCreateStruct->name = trim($layoutCreateStruct->name);
        $layoutCreateStruct->shared = $layoutCreateStruct->shared ? true : false;

        $createdLayoutId = $this->queryHandler->createLayout($layoutCreateStruct);

        return $this->loadLayout($createdLayoutId, $layoutCreateStruct->status);
    }

    /**
     * Updates a layout with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Layout $layout
     * @param \Netgen\BlockManager\Persistence\Values\LayoutUpdateStruct $layoutUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function updateLayout(Layout $layout, LayoutUpdateStruct $layoutUpdateStruct)
    {
        $layoutUpdateStruct->modified = $layoutUpdateStruct->modified !== null ?
            $layoutUpdateStruct->modified :
            $layout->modified;

        $layoutUpdateStruct->name = $layoutUpdateStruct->name !== null ?
            trim($layoutUpdateStruct->name) :
            $layout->name;

        $this->queryHandler->updateLayout(
            $layout->id,
            $layout->status,
            $layoutUpdateStruct
        );

        return $this->loadLayout($layout->id, $layout->status);
    }

    /**
     * Updates a specified zone.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Zone $zone
     * @param \Netgen\BlockManager\Persistence\Values\ZoneUpdateStruct $zoneUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Zone
     */
    public function updateZone(Zone $zone, ZoneUpdateStruct $zoneUpdateStruct)
    {
        $this->queryHandler->updateZone(
            $zone->layoutId,
            $zone->status,
            $zone->identifier,
            $zoneUpdateStruct
        );

        return $this->loadZone($zone->layoutId, $zone->status, $zone->identifier);
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
        $blockIds = $this->queryHandler->loadLayoutBlockIds($layoutId, $status);
        $this->blockHandler->deleteBlocks($blockIds, $status);
        $this->queryHandler->deleteLayout($layoutId, $status);
    }
}
