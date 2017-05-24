<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler;
use Netgen\BlockManager\Persistence\Handler\BlockHandler as BaseBlockHandler;
use Netgen\BlockManager\Persistence\Handler\LayoutHandler as LayoutHandlerInterface;
use Netgen\BlockManager\Persistence\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\Zone;
use Netgen\BlockManager\Persistence\Values\Layout\ZoneCreateStruct;
use Netgen\BlockManager\Persistence\Values\Layout\ZoneUpdateStruct;

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
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout
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
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Zone
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
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout[]
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
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout[]
     */
    public function loadSharedLayouts($includeDrafts = false, $offset = 0, $limit = null)
    {
        $data = $this->queryHandler->loadLayoutsData($includeDrafts, true, $offset, $limit);

        return $this->layoutMapper->mapLayouts($data);
    }

    /**
     * Loads all layouts related to provided shared layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $sharedLayout
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout[]
     */
    public function loadRelatedLayouts(Layout $sharedLayout, $offset = 0, $limit = null)
    {
        $data = $this->queryHandler->loadRelatedLayoutsData($sharedLayout, $offset, $limit);

        return $this->layoutMapper->mapLayouts($data);
    }

    /**
     * Loads the count of layouts related to provided shared layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $sharedLayout
     *
     * @return int
     */
    public function getRelatedLayoutsCount(Layout $sharedLayout)
    {
        return $this->queryHandler->getRelatedLayoutsCount($sharedLayout);
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
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Zone[]
     */
    public function loadLayoutZones(Layout $layout)
    {
        return $this->layoutMapper->mapZones(
            $this->queryHandler->loadLayoutZonesData($layout)
        );
    }

    /**
     * Returns if layout with provided name exists.
     *
     * @param string $name
     * @param int|string $excludedLayoutId
     *
     * @return bool
     */
    public function layoutNameExists($name, $excludedLayoutId = null)
    {
        return $this->queryHandler->layoutNameExists($name, $excludedLayoutId);
    }

    /**
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\LayoutCreateStruct $layoutCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout
     */
    public function createLayout(LayoutCreateStruct $layoutCreateStruct)
    {
        $currentTimeStamp = time();

        $newLayout = new Layout(
            array(
                'type' => $layoutCreateStruct->type,
                'name' => trim($layoutCreateStruct->name),
                'description' => trim($layoutCreateStruct->description),
                'created' => $currentTimeStamp,
                'modified' => $currentTimeStamp,
                'status' => $layoutCreateStruct->status,
                'shared' => $layoutCreateStruct->shared ? true : false,
            )
        );

        return $this->queryHandler->createLayout($newLayout);
    }

    /**
     * Creates a zone in provided layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     * @param \Netgen\BlockManager\Persistence\Values\Layout\ZoneCreateStruct $zoneCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Zone
     */
    public function createZone(Layout $layout, ZoneCreateStruct $zoneCreateStruct)
    {
        $rootBlock = $this->blockHandler->createBlock(
            new BlockCreateStruct(
                array(
                    'layoutId' => $layout->id,
                    'status' => $layout->status,
                    'position' => null,
                    'definitionIdentifier' => '',
                    'viewType' => '',
                    'itemViewType' => '',
                    'name' => '',
                    'parameters' => array(),
                    'config' => array(),
                )
            )
        );

        $newZone = new Zone(
            array(
                'layoutId' => $layout->id,
                'status' => $layout->status,
                'rootBlockId' => $rootBlock->id,
                'identifier' => $zoneCreateStruct->identifier,
                'linkedLayoutId' => $zoneCreateStruct->linkedLayoutId,
                'linkedZoneIdentifier' => $zoneCreateStruct->linkedZoneIdentifier,
            )
        );

        $this->queryHandler->createZone($newZone);

        return $newZone;
    }

    /**
     * Updates a layout with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     * @param \Netgen\BlockManager\Persistence\Values\Layout\LayoutUpdateStruct $layoutUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout
     */
    public function updateLayout(Layout $layout, LayoutUpdateStruct $layoutUpdateStruct)
    {
        $updatedLayout = clone $layout;

        if ($layoutUpdateStruct->modified !== null) {
            $updatedLayout->modified = (int) $layoutUpdateStruct->modified;
        }

        if ($layoutUpdateStruct->name !== null) {
            $updatedLayout->name = trim($layoutUpdateStruct->name);
        }

        if ($layoutUpdateStruct->description !== null) {
            $updatedLayout->description = trim($layoutUpdateStruct->description);
        }

        $this->queryHandler->updateLayout($updatedLayout);

        return $updatedLayout;
    }

    /**
     * Updates a specified zone.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Zone $zone
     * @param \Netgen\BlockManager\Persistence\Values\Layout\ZoneUpdateStruct $zoneUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Zone
     */
    public function updateZone(Zone $zone, ZoneUpdateStruct $zoneUpdateStruct)
    {
        $updatedZone = clone $zone;

        if ($zoneUpdateStruct->linkedZone !== null) {
            // Linked zone other than a zone object indicates we want to remove the link
            $updatedZone->linkedLayoutId = null;
            $updatedZone->linkedZoneIdentifier = null;

            if ($zoneUpdateStruct->linkedZone instanceof Zone) {
                $updatedZone->linkedLayoutId = $zoneUpdateStruct->linkedZone->layoutId;
                $updatedZone->linkedZoneIdentifier = $zoneUpdateStruct->linkedZone->identifier;
            }
        }

        $this->queryHandler->updateZone($updatedZone);

        return $updatedZone;
    }

    /**
     * Copies the layout.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     * @param \Netgen\BlockManager\Persistence\Values\Layout\LayoutCopyStruct $layoutCopyStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout
     */
    public function copyLayout(Layout $layout, LayoutCopyStruct $layoutCopyStruct)
    {
        $copiedLayout = clone $layout;
        $copiedLayout->id = null;

        $currentTimeStamp = time();
        $copiedLayout->created = $currentTimeStamp;
        $copiedLayout->modified = $currentTimeStamp;

        if ($layoutCopyStruct->name !== null) {
            $copiedLayout->name = trim($layoutCopyStruct->name);
        }

        if ($layoutCopyStruct->description !== null) {
            $copiedLayout->description = trim($layoutCopyStruct->description);
        }

        $copiedLayout = $this->queryHandler->createLayout($copiedLayout);

        $layoutZones = $this->loadLayoutZones($layout);
        foreach ($layoutZones as $layoutZone) {
            $zoneCreateStruct = new ZoneCreateStruct(
                array(
                    'identifier' => $layoutZone->identifier,
                    'linkedLayoutId' => $layoutZone->linkedLayoutId,
                    'linkedZoneIdentifier' => $layoutZone->linkedZoneIdentifier,
                )
            );

            $createdZone = $this->createZone($copiedLayout, $zoneCreateStruct);
            $rootBlock = $this->blockHandler->loadBlock(
                $createdZone->rootBlockId,
                $createdZone->status
            );

            $zoneBlocks = $this->blockHandler->loadChildBlocks(
                $this->blockHandler->loadBlock($layoutZone->rootBlockId, $layoutZone->status)
            );

            foreach ($zoneBlocks as $block) {
                $this->blockHandler->copyBlock($block, $rootBlock, 'root');
            }
        }

        return $copiedLayout;
    }

    /**
     * Changes the provided layout type.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     * @param string $targetLayoutType
     * @param array $zoneMappings
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout
     */
    public function changeLayoutType(Layout $layout, $targetLayoutType, array $zoneMappings = array())
    {
        $newRootBlocks = array();
        $oldRootBlocks = array();
        $oldZones = $this->loadLayoutZones($layout);

        foreach ($oldZones as $zoneIdentifier => $oldZone) {
            $oldRootBlocks[$zoneIdentifier] = $this->blockHandler->loadBlock(
                $oldZone->rootBlockId,
                $oldZone->status
            );
        }

        foreach ($zoneMappings as $newZoneIdentifier => $mappedZones) {
            $newRootBlocks[$newZoneIdentifier] = $this->blockHandler->createBlock(
                new BlockCreateStruct(
                    array(
                        'layoutId' => $layout->id,
                        'status' => $layout->status,
                        'position' => null,
                        'definitionIdentifier' => '',
                        'viewType' => '',
                        'itemViewType' => '',
                        'name' => '',
                        'parameters' => array(),
                        'config' => array(),
                    )
                )
            );

            $i = 0;
            foreach ($mappedZones as $mappedZone) {
                $blocks = $this->blockHandler->loadChildBlocks($oldRootBlocks[$mappedZone]);
                foreach ($blocks as $block) {
                    $this->blockHandler->moveBlock($block, $newRootBlocks[$newZoneIdentifier], 'root', $i);
                    ++$i;
                }
            }
        }

        foreach ($oldZones as $oldZone) {
            $this->queryHandler->deleteZone($oldZone->layoutId, $oldZone->identifier, $oldZone->status);
            $this->blockHandler->deleteBlock($oldRootBlocks[$oldZone->identifier]);
        }

        foreach ($newRootBlocks as $newZoneIdentifier => $rootBlock) {
            $newZone = new Zone(
                array(
                    'layoutId' => $layout->id,
                    'status' => $layout->status,
                    'rootBlockId' => $rootBlock->id,
                    'identifier' => $newZoneIdentifier,
                    'linkedLayoutId' => null,
                    'linkedZoneIdentifier' => null,
                )
            );

            $this->queryHandler->createZone($newZone);
        }

        $newLayout = clone $layout;
        $newLayout->type = $targetLayoutType;

        $this->queryHandler->updateLayout($newLayout);

        return $newLayout;
    }

    /**
     * Creates a new layout status.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\Layout\Layout
     */
    public function createLayoutStatus(Layout $layout, $newStatus)
    {
        $currentTimeStamp = time();

        $newLayout = clone $layout;
        $newLayout->status = $newStatus;
        $newLayout->created = $currentTimeStamp;
        $newLayout->modified = $currentTimeStamp;

        $this->queryHandler->createLayout($newLayout);

        $layoutBlocks = $this->blockHandler->loadLayoutBlocks($layout);
        foreach ($layoutBlocks as $block) {
            $this->blockHandler->createBlockStatus($block, $newStatus);
        }

        $layoutZones = $this->loadLayoutZones($layout);
        foreach ($layoutZones as $layoutZone) {
            $newZone = clone $layoutZone;
            $newZone->status = $newStatus;

            $this->queryHandler->createZone($newZone);
        }

        return $newLayout;
    }

    /**
     * Deletes a layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     */
    public function deleteLayout($layoutId, $status = null)
    {
        $this->queryHandler->deleteLayoutZones($layoutId, $status);
        $this->blockHandler->deleteLayoutBlocks($layoutId, $status);
        $this->queryHandler->deleteLayout($layoutId, $status);
    }
}
