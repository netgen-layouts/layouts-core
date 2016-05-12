<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Handler;

use Netgen\BlockManager\API\Values\BlockCreateStruct;
use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper;
use Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper;
use Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\BlockMapper;
use Netgen\BlockManager\Persistence\Handler\BlockHandler as BlockHandlerInterface;
use Netgen\BlockManager\Persistence\Handler\CollectionHandler as CollectionHandlerInterface;
use Netgen\BlockManager\API\Exception\NotFoundException;
use Doctrine\DBAL\Types\Type;

class BlockHandler implements BlockHandlerInterface
{
    /**
     * @var \Netgen\BlockManager\Persistence\Handler\CollectionHandler
     */
    protected $collectionHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\BlockMapper
     */
    protected $blockMapper;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper
     */
    protected $connectionHelper;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper
     */
    protected $positionHelper;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper
     */
    protected $queryHelper;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Handler\CollectionHandler $collectionHandler
     * @param \Netgen\BlockManager\Persistence\Doctrine\Mapper\BlockMapper $blockMapper
     * @param \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper $connectionHelper
     * @param \Netgen\BlockManager\Persistence\Doctrine\Helper\PositionHelper $positionHelper
     * @param \Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper $queryHelper
     */
    public function __construct(
        CollectionHandlerInterface $collectionHandler,
        BlockMapper $blockMapper,
        ConnectionHelper $connectionHelper,
        PositionHelper $positionHelper,
        QueryHelper $queryHelper
    ) {
        $this->collectionHandler = $collectionHandler;
        $this->blockMapper = $blockMapper;
        $this->connectionHelper = $connectionHelper;
        $this->positionHelper = $positionHelper;
        $this->queryHelper = $queryHelper;
    }

    /**
     * Loads a block with specified ID.
     *
     * @param int|string $blockId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If block with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function loadBlock($blockId, $status)
    {
        $query = $this->queryHelper->getBlockSelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $blockId, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            throw new NotFoundException('block', $blockId);
        }

        $data = $this->blockMapper->mapBlocks($data);

        return reset($data);
    }

    /**
     * Loads all blocks from zone with specified identifier.
     *
     * @param int|string $layoutId
     * @param string $zoneIdentifier
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block[]
     */
    public function loadZoneBlocks($layoutId, $zoneIdentifier, $status)
    {
        $query = $this->queryHelper->getBlockSelectQuery();
        $query->where(
                $query->expr()->andX(
                    $query->expr()->eq('layout_id', ':layout_id'),
                    $query->expr()->eq('zone_identifier', ':zone_identifier')
                )
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER)
            ->setParameter('zone_identifier', $zoneIdentifier, Type::STRING)
            ->orderBy('position', 'ASC');

        $this->queryHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            return array();
        }

        return $this->blockMapper->mapBlocks($data);
    }

    /**
     * Loads all collection references belonging to the provided block.
     *
     * @param int|string $blockId
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\CollectionReference[]
     */
    public function loadBlockCollections($blockId, $status)
    {
        $query = $this->queryHelper->getQuery();
        $query->select('block_id', 'status', 'collection_id', 'identifier', 'start', 'length')
            ->from('ngbm_block_collection')
            ->where(
                $query->expr()->eq('block_id', ':block_id')
            )
            ->setParameter('block_id', $blockId, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            return array();
        }

        return $this->blockMapper->mapCollectionReferences($data);
    }

    /**
     * Creates a block in specified layout and zone.
     *
     * @param \Netgen\BlockManager\API\Values\BlockCreateStruct $blockCreateStruct
     * @param int|string $layoutId
     * @param string $zoneIdentifier
     * @param int $status
     * @param int $position
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If provided position is out of range
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function createBlock(BlockCreateStruct $blockCreateStruct, $layoutId, $zoneIdentifier, $status, $position = null)
    {
        $position = $this->positionHelper->createPosition(
            $this->getPositionHelperConditions(
                $layoutId,
                $zoneIdentifier,
                $status
            ),
            $position
        );

        $query = $this->queryHelper->getBlockInsertQuery(
            array(
                'status' => $status,
                'layout_id' => $layoutId,
                'zone_identifier' => $zoneIdentifier,
                'position' => $position,
                'definition_identifier' => $blockCreateStruct->definitionIdentifier,
                'view_type' => $blockCreateStruct->viewType,
                'name' => $blockCreateStruct->name !== null ? trim($blockCreateStruct->name) : '',
                'parameters' => $blockCreateStruct->getParameters(),
            )
        );

        $query->execute();

        $createdBlock = $this->loadBlock(
            (int)$this->connectionHelper->lastInsertId('ngbm_block'),
            $status
        );

        return $createdBlock;
    }

    /**
     * Updates a block with specified ID.
     *
     * @param int|string $blockId
     * @param int $status
     * @param \Netgen\BlockManager\API\Values\BlockUpdateStruct $blockUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function updateBlock($blockId, $status, BlockUpdateStruct $blockUpdateStruct)
    {
        $block = $this->loadBlock($blockId, $status);
        $parameters = $blockUpdateStruct->getParameters() + $block->parameters;

        $query = $this->queryHelper->getQuery();
        $query
            ->update('ngbm_block')
            ->set('view_type', ':view_type')
            ->set('name', ':name')
            ->set('parameters', ':parameters')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $blockId, Type::INTEGER)
            ->setParameter('view_type', $blockUpdateStruct->viewType !== null ? $blockUpdateStruct->viewType : $block->viewType, Type::STRING)
            ->setParameter('name', $blockUpdateStruct->name !== null ? trim($blockUpdateStruct->name) : $block->name, Type::STRING)
            ->setParameter('parameters', $parameters, Type::JSON_ARRAY);

        $this->queryHelper->applyStatusCondition($query, $status);

        $query->execute();

        return $this->loadBlock($blockId, $status);
    }

    /**
     * Copies a block with specified ID to a zone with specified identifier.
     *
     * @param int|string $blockId
     * @param int $status
     * @param string $zoneIdentifier
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function copyBlock($blockId, $status, $zoneIdentifier)
    {
        $block = $this->loadBlock($blockId, $status);

        $query = $this->queryHelper->getBlockInsertQuery(
            array(
                'status' => $block->status,
                'layout_id' => $block->layoutId,
                'zone_identifier' => $zoneIdentifier,
                'position' => $this->positionHelper->getNextPosition(
                    $this->getPositionHelperConditions(
                        $block->layoutId,
                        $zoneIdentifier,
                        $status
                    )
                ),
                'definition_identifier' => $block->definitionIdentifier,
                'view_type' => $block->viewType,
                'name' => $block->name,
                'parameters' => $block->parameters,
            )
        );

        $query->execute();

        $copiedBlockId = (int)$this->connectionHelper->lastInsertId('ngbm_block');

        $collectionReferences = $this->loadBlockCollections($blockId, $status);
        foreach ($collectionReferences as $collectionReference) {
            $collection = $this->collectionHandler->loadCollection(
                $collectionReference->collectionId,
                $status
            );

            if (!$this->collectionHandler->isNamedCollection($collectionReference->collectionId)) {
                $newCollectionId = $this->collectionHandler->copyCollection($collection->id, $status);
            } else {
                $newCollectionId = $collectionReference->collectionId;
            }

            $this->addCollectionToBlock(
                $copiedBlockId,
                $block->status,
                $newCollectionId,
                $collectionReference->identifier,
                $collectionReference->offset,
                $collectionReference->limit
            );
        }

        return $this->loadBlock($copiedBlockId, $block->status);
    }

    /**
     * Moves a block to specified position in the zone.
     *
     * @param int|string $blockId
     * @param int $status
     * @param int $position
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If provided position is out of range
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function moveBlock($blockId, $status, $position)
    {
        $block = $this->loadBlock($blockId, $status);

        $position = $this->positionHelper->moveToPosition(
            $this->getPositionHelperConditions(
                $block->layoutId,
                $block->zoneIdentifier,
                $status
            ),
            $block->position,
            $position
        );

        $query = $this->queryHelper->getQuery();

        $query
            ->update('ngbm_block')
            ->set('position', ':position')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $block->id, Type::INTEGER)
            ->setParameter('position', $position, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $query->execute();

        $movedBlock = $this->loadBlock($blockId, $status);

        return $movedBlock;
    }

    /**
     * Moves a block to specified position in a specified zone.
     *
     * @param int|string $blockId
     * @param int $status
     * @param string $zoneIdentifier
     * @param int $position
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If provided position is out of range
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function moveBlockToZone($blockId, $status, $zoneIdentifier, $position)
    {
        $block = $this->loadBlock($blockId, $status);

        $position = $this->positionHelper->createPosition(
            $this->getPositionHelperConditions(
                $block->layoutId,
                $zoneIdentifier,
                $status
            ),
            $position
        );

        $query = $this->queryHelper->getQuery();

        $query
            ->update('ngbm_block')
            ->set('zone_identifier', ':zone_identifier')
            ->set('position', ':position')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $block->id, Type::INTEGER)
            ->setParameter('zone_identifier', $zoneIdentifier, Type::STRING)
            ->setParameter('position', $position, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $query->execute();

        $this->positionHelper->removePosition(
            $this->getPositionHelperConditions(
                $block->layoutId,
                $block->zoneIdentifier,
                $status
            ),
            $block->position
        );

        $movedBlock = $this->loadBlock($blockId, $status);

        return $movedBlock;
    }

    /**
     * Deletes a block with specified ID.
     *
     * @param int|string $blockId
     * @param int $status
     */
    public function deleteBlock($blockId, $status)
    {
        $block = $this->loadBlock($blockId, $status);

        $collectionReferences = $this->loadBlockCollections(
            $blockId,
            $status
        );

        foreach ($collectionReferences as $collectionReference) {
            $this->removeCollectionFromBlock(
                $blockId,
                $status,
                $collectionReference->collectionId
            );

            if (!$this->collectionHandler->isNamedCollection($collectionReference->collectionId)) {
                $this->collectionHandler->deleteCollection($collectionReference->collectionId, $status);
            }
        }

        $query = $this->queryHelper->getQuery();

        $query->delete('ngbm_block')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $blockId, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $query->execute();

        $this->positionHelper->removePosition(
            $this->getPositionHelperConditions(
                $block->layoutId,
                $block->zoneIdentifier,
                $status
            ),
            $block->position
        );
    }

    /**
     * Returns if collection with provided ID already exists in the block.
     *
     * @param int|string $blockId
     * @param int $status
     * @param int|string $collectionId
     *
     * @return bool
     */
    public function collectionExists($blockId, $status, $collectionId)
    {
        $query = $this->queryHelper->getQuery();
        $query->select('count(*) AS count')
            ->from('ngbm_block_collection')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('block_id', ':block_id'),
                    $query->expr()->eq('collection_id', ':collection_id')
                )
            )
            ->setParameter('block_id', $blockId, Type::INTEGER)
            ->setParameter('collection_id', $collectionId, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
    }

    /**
     * Returns if provided collection identifier already exists in the block.
     *
     * @param int|string $blockId
     * @param int $status
     * @param string $identifier
     *
     * @return bool
     */
    public function collectionIdentifierExists($blockId, $status, $identifier)
    {
        $query = $this->queryHelper->getQuery();
        $query->select('count(*) AS count')
            ->from('ngbm_block_collection')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('block_id', ':block_id'),
                    $query->expr()->eq('identifier', ':identifier')
                )
            )
            ->setParameter('block_id', $blockId, Type::INTEGER)
            ->setParameter('identifier', $identifier, Type::STRING);

        $this->queryHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
    }

    /**
     * Adds the collection to the block.
     *
     * @param int|string $blockId
     * @param int $status
     * @param int|string $collectionId
     * @param string $identifier
     * @param int $offset
     * @param int $limit
     */
    public function addCollectionToBlock($blockId, $status, $collectionId, $identifier, $offset = 0, $limit = null)
    {
        $query = $this->queryHelper->getQuery();

        $query->insert('ngbm_block_collection')
            ->values(
                array(
                    'block_id' => ':block_id',
                    'status' => ':status',
                    'collection_id' => ':collection_id',
                    'identifier' => ':identifier',
                    'start' => ':start',
                    'length' => ':length',
                )
            )
            ->setParameter('block_id', $blockId, Type::INTEGER)
            ->setParameter('status', $status, Type::INTEGER)
            ->setParameter('collection_id', $collectionId, Type::INTEGER)
            ->setParameter('identifier', $identifier, Type::STRING)
            ->setParameter('start', $offset, Type::INTEGER)
            ->setParameter('length', $limit, Type::INTEGER);

        $query->execute();
    }

    /**
     * Removes the collection from the block.
     *
     * @param int|string $blockId
     * @param int $status
     * @param int|string $collectionId
     */
    public function removeCollectionFromBlock($blockId, $status, $collectionId)
    {
        $query = $this->queryHelper->getQuery();

        $query->delete('ngbm_block_collection')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('block_id', ':block_id'),
                    $query->expr()->eq('collection_id', ':collection_id')
                )
            )
            ->setParameter('block_id', $blockId, Type::INTEGER)
            ->setParameter('collection_id', $collectionId, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Builds the condition array that will be used with position helper.
     *
     * @param int|string $layoutId
     * @param string $zoneIdentifier
     * @param int $status
     *
     * @return array
     */
    protected function getPositionHelperConditions($layoutId, $zoneIdentifier, $status)
    {
        return array(
            'table' => 'ngbm_block',
            'column' => 'position',
            'conditions' => array(
                'layout_id' => $layoutId,
                'zone_identifier' => $zoneIdentifier,
                'status' => $status,
            ),
        );
    }
}
