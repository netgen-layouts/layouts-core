<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler;
use Netgen\BlockManager\Persistence\Values\BlockCreateStruct;
use Netgen\BlockManager\Persistence\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Handler\BlockHandler as BaseBlockHandler;
use Netgen\BlockManager\Persistence\Handler\CollectionHandler as BaseCollectionHandler;
use Netgen\BlockManager\Persistence\Handler\LayoutHandler as LayoutHandlerInterface;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper;
use Netgen\BlockManager\API\Values\LayoutCreateStruct as APILayoutCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutCreateStruct;
use Netgen\BlockManager\Exception\NotFoundException;

class LayoutHandler implements LayoutHandlerInterface
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutQueryHandler
     */
    protected $queryHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler
     */
    protected $blockQueryHandler;

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
     * @param \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\BlockQueryHandler $blockQueryHandler
     * @param \Netgen\BlockManager\Persistence\Handler\BlockHandler $blockHandler
     * @param \Netgen\BlockManager\Persistence\Handler\CollectionHandler $collectionHandler
     * @param \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutMapper $layoutMapper
     */
    public function __construct(
        LayoutQueryHandler $queryHandler,
        BlockQueryHandler $blockQueryHandler,
        BaseBlockHandler $blockHandler,
        BaseCollectionHandler $collectionHandler,
        LayoutMapper $layoutMapper
    ) {
        $this->queryHandler = $queryHandler;
        $this->blockQueryHandler = $blockQueryHandler;
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
     * @param string $identifier
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If layout with specified ID or zone with specified identifier do not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Zone
     */
    public function loadZone($layoutId, $identifier, $status)
    {
        $data = $this->queryHandler->loadZoneData($layoutId, $identifier, $status);

        if (empty($data)) {
            throw new NotFoundException('zone', $identifier);
        }

        $data = $this->layoutMapper->mapZones($data);

        return reset($data);
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
     * @param string $identifier
     * @param int $status
     *
     * @return bool
     */
    public function zoneExists($layoutId, $identifier, $status)
    {
        return $this->queryHandler->zoneExists($layoutId, $identifier, $status);
    }

    /**
     * Loads all zones that belong to layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Zone[]
     */
    public function loadLayoutZones($layoutId, $status)
    {
        return $this->layoutMapper->mapZones(
            $this->queryHandler->loadLayoutZonesData($layoutId, $status)
        );
    }

    /**
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutCreateStruct $layoutCreateStruct
     * @param array $zoneIdentifiers
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function createLayout(APILayoutCreateStruct $layoutCreateStruct, array $zoneIdentifiers = array())
    {
        $createdLayoutId = $this->queryHandler->createLayout(
            new LayoutCreateStruct(
                array(
                    'type' => $layoutCreateStruct->type,
                    'name' => trim($layoutCreateStruct->name),
                    'status' => $layoutCreateStruct->status,
                    'zoneIdentifiers' => array_unique($zoneIdentifiers),
                )
            )
        );

        return $this->loadLayout($createdLayoutId, $layoutCreateStruct->status);
    }

    /**
     * Copies a layout with specified ID.
     *
     * @param int|string $layoutId
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function copyLayout($layoutId)
    {
        // First copy layout and zone data
        $insertedLayoutId = null;
        $zoneIdentifiers = null;

        $layoutData = $this->queryHandler->loadLayoutData($layoutId);
        foreach ($layoutData as $layoutDataRow) {
            if ($zoneIdentifiers === null) {
                $zoneIdentifiers = array_map(
                    function (array $zoneDataRow) {
                        return $zoneDataRow['identifier'];
                    },
                    $this->queryHandler->loadLayoutZonesData($layoutId, $layoutDataRow['status'])
                );
            }

            $insertedLayoutId = $this->queryHandler->createLayout(
                new LayoutCreateStruct(
                    array(
                        'type' => $layoutDataRow['type'],
                        'name' => $layoutDataRow['name'] . ' (copy) ' . crc32(microtime()),
                        'status' => $layoutDataRow['status'],
                        'zoneIdentifiers' => $zoneIdentifiers,
                    )
                ),
                $insertedLayoutId
            );
        }

        // Then copy block data

        $blockIdMapping = array();
        foreach ($zoneIdentifiers as $zoneIdentifier) {
            $blockData = $this->blockQueryHandler->loadZoneBlocksData($layoutId, $zoneIdentifier);

            foreach ($blockData as $blockDataRow) {
                $createdBlockId = $this->blockQueryHandler->createBlock(
                    new BlockCreateStruct(
                        array(
                            'layoutId' => $insertedLayoutId,
                            'zoneIdentifier' => $blockDataRow['zone_identifier'],
                            'status' => $blockDataRow['status'],
                            'position' => $blockDataRow['position'],
                            'definitionIdentifier' => $blockDataRow['definition_identifier'],
                            'viewType' => $blockDataRow['view_type'],
                            'name' => $blockDataRow['name'],
                            'parameters' => $blockDataRow['parameters'],
                        )
                    ),
                    isset($blockIdMapping[$blockDataRow['id']]) ?
                        $blockIdMapping[$blockDataRow['id']] :
                        null
                );

                if (!isset($blockIdMapping[$blockDataRow['id']])) {
                    $blockIdMapping[$blockDataRow['id']] = $createdBlockId;
                }
            }
        }

        $collectionIdMapping = array();

        foreach ($blockIdMapping as $oldBlockId => $newBlockId) {
            $collectionsData = $this->blockQueryHandler->loadCollectionReferencesData($oldBlockId);
            foreach ($collectionsData as $collectionsDataRow) {
                if (!isset($collectionIdMapping[$collectionsDataRow['collection_id']])) {
                    if (!$this->collectionHandler->isNamedCollection($collectionsDataRow['collection_id'], $collectionsDataRow['collection_status'])) {
                        $copiedCollectionId = $this->collectionHandler->copyCollection(
                            $collectionsDataRow['collection_id']
                        );

                        $collectionIdMapping[$collectionsDataRow['collection_id']] = $copiedCollectionId;
                    } else {
                        $collectionIdMapping[$collectionsDataRow['collection_id']] = $collectionsDataRow['collection_id'];
                    }
                }

                $this->blockHandler->addCollectionToBlock(
                    $newBlockId,
                    $collectionsDataRow['block_status'],
                    $collectionIdMapping[$collectionsDataRow['collection_id']],
                    $collectionsDataRow['collection_status'],
                    $collectionsDataRow['identifier'],
                    $collectionsDataRow['start'],
                    $collectionsDataRow['length']
                );
            }
        }

        return $this->loadLayout($insertedLayoutId, Layout::STATUS_PUBLISHED);
    }

    /**
     * Creates a new layout status.
     *
     * @param int|string $layoutId
     * @param int $status
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function createLayoutStatus($layoutId, $status, $newStatus)
    {
        $zoneIdentifiers = array_map(
            function (array $zoneDataRow) {
                return $zoneDataRow['identifier'];
            },
            $this->queryHandler->loadLayoutZonesData($layoutId, $status)
        );

        $layoutData = $this->queryHandler->loadLayoutData($layoutId, $status);

        $this->queryHandler->createLayout(
            new LayoutCreateStruct(
                array(
                    'type' => $layoutData[0]['type'],
                    'name' => $layoutData[0]['name'],
                    'status' => $newStatus,
                    'zoneIdentifiers' => $zoneIdentifiers,
                )
            ),
            $layoutData[0]['id']
        );

        foreach ($zoneIdentifiers as $zoneIdentifier) {
            $blockData = $this->blockQueryHandler->loadZoneBlocksData($layoutData[0]['id'], $zoneIdentifier, $status);
            foreach ($blockData as $blockDataRow) {
                $this->blockHandler->createBlockStatus($blockDataRow['id'], $status, $newStatus);
            }
        }

        return $this->loadLayout($layoutData[0]['id'], $newStatus);
    }

    /**
     * Deletes a layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     */
    public function deleteLayout($layoutId, $status = null)
    {
        // First delete all non named collections
        $collectionData = $this->queryHandler->loadLayoutCollectionsData($layoutId, $status);

        foreach ($collectionData as $collectionDataRow) {
            $this->blockHandler->removeCollectionFromBlock(
                $collectionDataRow['block_id'],
                $collectionDataRow['block_status'],
                $collectionDataRow['collection_id'],
                $collectionDataRow['collection_status']
            );

            if (!$this->collectionHandler->isNamedCollection($collectionDataRow['collection_id'], $collectionDataRow['collection_status'])) {
                $this->collectionHandler->deleteCollection($collectionDataRow['collection_id'], $collectionDataRow['collection_status']);
            }
        }

        $this->queryHandler->deleteLayoutBlocks($layoutId, $status);
        $this->queryHandler->deleteLayout($layoutId, $status);
    }
}
