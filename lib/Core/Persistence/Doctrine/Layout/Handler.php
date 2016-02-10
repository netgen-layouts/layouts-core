<?php

namespace Netgen\BlockManager\Core\Persistence\Doctrine\Layout;

use Doctrine\DBAL\Query\QueryBuilder;
use Netgen\BlockManager\API\Values\BlockCreateStruct;
use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use Netgen\BlockManager\Core\Persistence\Doctrine\Connection\Helper;
use Netgen\BlockManager\Persistence\Handler\Layout as LayoutHandlerInterface;
use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Exception\NotFoundException;
use Netgen\BlockManager\API\Exception\BadStateException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

class Handler implements LayoutHandlerInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @var \Netgen\BlockManager\Core\Persistence\Doctrine\Connection\Helper
     */
    protected $connectionHelper;

    /**
     * @var \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Mapper
     */
    protected $mapper;

    /**
     * Constructor.
     *
     * @param \Doctrine\DBAL\Connection $connection
     * @param \Netgen\BlockManager\Core\Persistence\Doctrine\Connection\Helper $connectionHelper
     * @param \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Mapper $mapper
     */
    public function __construct(Connection $connection, Helper $connectionHelper, Mapper $mapper)
    {
        $this->connection = $connection;
        $this->connectionHelper = $connectionHelper;
        $this->mapper = $mapper;
    }

    /**
     * Loads a layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function loadLayout($layoutId, $status)
    {
        $query = $this->createLayoutSelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $layoutId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            throw new NotFoundException('layout', $layoutId);
        }

        $data = $this->mapper->mapLayouts($data);

        return reset($data);
    }

    /**
     * Loads a zone with specified identifier.
     *
     * @param int|string $layoutId
     * @param string $identifier
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If layout with specified ID or zone with specified identifier do not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Zone
     */
    public function loadZone($layoutId, $identifier, $status)
    {
        $query = $this->createZoneSelectQuery();
        $query->where(
            $query->expr()->andX(
                $query->expr()->eq('layout_id', ':layout_id'),
                $query->expr()->eq('identifier', ':identifier')
            )
        )
        ->setParameter('layout_id', $layoutId, Type::INTEGER)
        ->setParameter('identifier', $identifier, Type::STRING);

        $this->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            throw new NotFoundException('zone', $identifier);
        }

        $data = $this->mapper->mapZones($data);

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
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('ngbm_layout')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $layoutId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
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
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('ngbm_zone')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('identifier', ':identifier'),
                    $query->expr()->eq('layout_id', ':layout_id')
                )
            )
            ->setParameter('identifier', $identifier, Type::STRING)
            ->setParameter('layout_id', $layoutId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
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
        $query = $this->createZoneSelectQuery();
        $query->where(
            $query->expr()->eq('layout_id', ':layout_id')
        )
        ->orderBy('identifier', 'ASC')
        ->setParameter('layout_id', $layoutId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            return array();
        }

        return $this->mapper->mapZones($data);
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
        $query = $this->createBlockSelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $blockId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            throw new NotFoundException('block', $blockId);
        }

        $data = $this->mapper->mapBlocks($data);

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
        $query = $this->createBlockSelectQuery();
        $query->where(
                $query->expr()->andX(
                    $query->expr()->eq('layout_id', ':layout_id'),
                    $query->expr()->eq('zone_identifier', ':zone_identifier')
                )
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER)
            ->setParameter('zone_identifier', $zoneIdentifier, Type::STRING)
            ->orderBy('position', 'ASC');

        $this->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            return array();
        }

        return $this->mapper->mapBlocks($data);
    }

    /**
     * Creates a layout.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutCreateStruct $layoutCreateStruct
     * @param int|string $parentLayoutId
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function createLayout(LayoutCreateStruct $layoutCreateStruct, $parentLayoutId = null)
    {
        $currentTimeStamp = time();

        $query = $this->createLayoutInsertQuery(
            array(
                'parent_id' => $parentLayoutId,
                'identifier' => $layoutCreateStruct->identifier,
                'name' => $layoutCreateStruct->name,
                'created' => $currentTimeStamp,
                'modified' => $currentTimeStamp,
                'status' => $layoutCreateStruct->status,
            )
        );

        $query->execute();

        $createdLayoutId = (int)$this->connectionHelper->lastInsertId('ngbm_layout');

        foreach ($layoutCreateStruct->zoneIdentifiers as $zoneIdentifier) {
            $zoneQuery = $this->createZoneInsertQuery(
                array(
                    'identifier' => $zoneIdentifier,
                    'layout_id' => $createdLayoutId,
                    'status' => $layoutCreateStruct->status,
                )
            );

            $zoneQuery->execute();
        }

        return $this->loadLayout($createdLayoutId, $layoutCreateStruct->status);
    }

    /**
     * Creates a block in specified layout and zone.
     *
     * @param \Netgen\BlockManager\API\Values\BlockCreateStruct $blockCreateStruct
     * @param int|string $layoutId
     * @param string $zoneIdentifier
     * @param int $status
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If zone does not exist in the layout
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function createBlock(BlockCreateStruct $blockCreateStruct, $layoutId, $zoneIdentifier, $status)
    {
        if (!$this->zoneExists($layoutId, $zoneIdentifier, $status)) {
            throw new BadStateException('zoneIdentifier', 'Zone with provided identifier does not exist in the layout.');
        }

        if ($blockCreateStruct->position === null) {
            $blockCreateStruct->position = $this->getNextBlockPosition(
                $layoutId,
                $zoneIdentifier,
                $status
            );
        }

        $this->incrementBlockPositions(
            $layoutId,
            $zoneIdentifier,
            $status,
            $blockCreateStruct->position
        );

        $query = $this->createBlockInsertQuery(
            array(
                'layout_id' => $layoutId,
                'zone_identifier' => $zoneIdentifier,
                'position' => $blockCreateStruct->position,
                'definition_identifier' => $blockCreateStruct->definitionIdentifier,
                'view_type' => $blockCreateStruct->viewType,
                'name' => $blockCreateStruct->name,
                'parameters' => $blockCreateStruct->getParameters(),
                'status' => $status,
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
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_block')
            ->set('view_type', ':view_type')
            ->set('name', ':name')
            ->set('parameters', ':parameters')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $blockId, Type::INTEGER)
            ->setParameter('view_type', $blockUpdateStruct->viewType, Type::STRING)
            ->setParameter('name', trim($blockUpdateStruct->name), Type::STRING)
            ->setParameter('parameters', $blockUpdateStruct->getParameters(), Type::JSON_ARRAY);

        $this->applyStatusCondition($query, $status);

        $query->execute();

        return $this->loadBlock($blockId, $status);
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
    }

    /**
     * Creates a new layout status.
     *
     * @param int|string $layoutId
     * @param int $status
     * @param int $newStatus
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If layout already has the provided status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function createLayoutStatus($layoutId, $status, $newStatus)
    {
        if ($this->layoutExists($layoutId, $newStatus)) {
            throw new BadStateException('newStatus', 'Layout already has the provided status.');
        }

        $layout = $this->loadLayout($layoutId, $status);

        $currentTimeStamp = time();
        $query = $this->createLayoutInsertQuery(
            array(
                'parent_id' => $layout->parentId,
                'identifier' => $layout->identifier,
                'name' => $layout->name,
                'created' => $currentTimeStamp,
                'modified' => $currentTimeStamp,
                'status' => $newStatus,
            ),
            $layout->id
        );

        $query->execute();

        $layoutZones = $this->loadLayoutZones($layout->id, $status);
        foreach ($layoutZones as $zone) {
            $zoneQuery = $this->createZoneInsertQuery(
                array(
                    'identifier' => $zone->identifier,
                    'layout_id' => $layout->id,
                    'status' => $newStatus,
                )
            );

            $zoneQuery->execute();

            $zoneBlocks = $this->loadZoneBlocks($layout->id, $zone->identifier, $status);
            foreach ($zoneBlocks as $block) {
                $blockQuery = $this->createBlockInsertQuery(
                    array(
                        'layout_id' => $layout->id,
                        'zone_identifier' => $zone->identifier,
                        'position' => $block->position,
                        'definition_identifier' => $block->definitionIdentifier,
                        'view_type' => $block->viewType,
                        'name' => $block->name,
                        'parameters' => $block->parameters,
                        'status' => $newStatus,
                    ),
                    $block->id
                );

                $blockQuery->execute();

                // @TODO: Copy block items
            }
        }

        return $this->loadLayout($layout->id, $newStatus);
    }

    /**
     * Updates the layout from one status to another.
     *
     * @param int $layoutId
     * @param int $status
     * @param int $newStatus
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If layout already has the provided status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function updateLayoutStatus($layoutId, $status, $newStatus)
    {
        if ($this->layoutExists($layoutId, $newStatus)) {
            throw new BadStateException('newStatus', 'Layout already has the provided status.');
        }

        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_layout')
            ->set('status', ':new_status')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $layoutId, Type::INTEGER)
            ->setParameter('new_status', $newStatus, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $query->execute();

        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_zone')
            ->set('status', ':new_status')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER)
            ->setParameter('new_status', $newStatus, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $query->execute();

        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_block')
            ->set('status', ':new_status')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER)
            ->setParameter('new_status', $newStatus, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $query->execute();

        // @TODO Update status of block items

        return $this->loadLayout($layoutId, $newStatus);
    }

    /**
     * Copies a block with specified ID to a zone with specified identifier.
     *
     * @param int|string $blockId
     * @param int $status
     * @param string $zoneIdentifier
     *
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If zone does not exist in the layout
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function copyBlock($blockId, $status, $zoneIdentifier = null)
    {
        $block = $this->loadBlock($blockId, $status);

        if ($zoneIdentifier === null) {
            $zoneIdentifier = $block->zoneIdentifier;
        }

        if (!$this->zoneExists($block->layoutId, $zoneIdentifier, $status)) {
            throw new BadStateException('zoneIdentifier', 'Zone with provided identifier does not exist in the layout.');
        }

        $query = $this->createBlockInsertQuery(
            array(
                'layout_id' => $block->layoutId,
                'zone_identifier' => $zoneIdentifier,
                'position' => $this->getNextBlockPosition($block->layoutId, $zoneIdentifier, $status),
                'definition_identifier' => $block->definitionIdentifier,
                'view_type' => $block->viewType,
                'name' => $block->name,
                'parameters' => $block->parameters,
                'status' => $block->status,
            )
        );

        $query->execute();

        // @TODO: Copy block items

        return $this->loadBlock(
            (int)$this->connectionHelper->lastInsertId('ngbm_block'),
            $block->status
        );
    }

    /**
     * Moves a block to specified position in the zone.
     *
     * @param int|string $blockId
     * @param int $status
     * @param int $position
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function moveBlock($blockId, $status, $position)
    {
        // @TODO Handle positions larger or equal than count of blocks in zone

        $block = $this->loadBlock($blockId, $status);

        if ($position > $block->position) {
            $this->decrementBlockPositions(
                $block->layoutId,
                $block->zoneIdentifier,
                $status,
                $block->position + 1,
                $position
            );
        } elseif ($position < $block->position) {
            $this->incrementBlockPositions(
                $block->layoutId,
                $block->zoneIdentifier,
                $status,
                $position,
                $block->position - 1
            );
        }

        $query = $this->connection->createQueryBuilder();

        $query
            ->update('ngbm_block')
            ->set('position', ':position')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $block->id, Type::INTEGER)
            ->setParameter('position', $position, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

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
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If zone does not exist in the layout
     *                                                              If block is already in specified zone
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function moveBlockToZone($blockId, $status, $zoneIdentifier, $position)
    {
        // @TODO Handle positions larger than count of blocks in zone

        $block = $this->loadBlock($blockId, $status);

        if (!$this->zoneExists($block->layoutId, $zoneIdentifier, $status)) {
            throw new BadStateException('zoneIdentifier', 'Zone with provided identifier does not exist in the layout.');
        }

        if ($zoneIdentifier === $block->zoneIdentifier) {
            throw new BadStateException('zoneIdentifier', 'Block is already in specified zone.');
        }

        $this->incrementBlockPositions(
            $block->layoutId,
            $zoneIdentifier,
            $status,
            $position
        );

        $query = $this->connection->createQueryBuilder();

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

        $this->applyStatusCondition($query, $status);

        $query->execute();

        $this->decrementBlockPositions(
            $block->layoutId,
            $block->zoneIdentifier,
            $status,
            $block->position
        );

        $movedBlock = $this->loadBlock($blockId, $status);

        return $movedBlock;
    }

    /**
     * Deletes a layout with specified ID.
     *
     * @param int|string $layoutId
     * @param int $status
     */
    public function deleteLayout($layoutId, $status = null)
    {
        // @TODO: Delete block items

        // First delete all blocks

        $query = $this->connection->createQueryBuilder();
        $query
            ->delete('ngbm_block')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();

        // Then delete all zones

        $query = $this->connection->createQueryBuilder();
        $query->delete('ngbm_zone')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();

        // Then delete the layout itself

        $query = $this->connection->createQueryBuilder();
        $query->delete('ngbm_layout')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $layoutId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();
    }

    /**
     * Deletes a block with specified ID.
     *
     * @param int|string $blockId
     * @param int $status
     */
    public function deleteBlock($blockId, $status)
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_block')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $blockId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $query->execute();

        // @TODO: Delete block items
    }

    /**
     * Applies status condition to the query.
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query
     * @param int $status
     */
    protected function applyStatusCondition(QueryBuilder $query, $status)
    {
        $query->andWhere($query->expr()->eq('status', ':status'))
            ->setParameter('status', $status, Type::INTEGER);
    }

    /**
     * Builds and returns a layout database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function createLayoutSelectQuery()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('id', 'parent_id', 'identifier', 'name', 'created', 'modified', 'status')
            ->from('ngbm_layout');

        return $query;
    }

    /**
     * Builds and returns a zone database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function createZoneSelectQuery()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('identifier', 'layout_id', 'status')
            ->from('ngbm_zone');

        return $query;
    }

    /**
     * Builds and returns a block database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function createBlockSelectQuery()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('id', 'layout_id', 'zone_identifier', 'position', 'definition_identifier', 'view_type', 'name', 'parameters', 'status')
            ->from('ngbm_block');

        return $query;
    }

    /**
     * Builds and returns a layout database INSERT query.
     *
     * @param array $parameters
     * @param int $layoutId
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function createLayoutInsertQuery(array $parameters, $layoutId = null)
    {
        return $this->connection->createQueryBuilder()
            ->insert('ngbm_layout')
            ->values(
                array(
                    'id' => ':id',
                    'parent_id' => ':parent_id',
                    'identifier' => ':identifier',
                    'name' => ':name',
                    'created' => ':created',
                    'modified' => ':modified',
                    'status' => ':status',
                )
            )
            ->setValue(
                'id',
                $layoutId !== null ? (int)$layoutId : $this->connectionHelper->getAutoIncrementValue('ngbm_layout')
            )
            ->setParameter('parent_id', $parameters['parent_id'], Type::INTEGER)
            ->setParameter('identifier', $parameters['identifier'], Type::STRING)
            ->setParameter('name', trim($parameters['name']), Type::STRING)
            ->setParameter('created', $parameters['created'], Type::INTEGER)
            ->setParameter('modified', $parameters['modified'], Type::INTEGER)
            ->setParameter('status', $parameters['status'], Type::INTEGER);
    }

    /**
     * Builds and returns a zone database INSERT query.
     *
     * @param array $parameters
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function createZoneInsertQuery(array $parameters)
    {
        return $this->connection->createQueryBuilder()
            ->insert('ngbm_zone')
            ->values(
                array(
                    'identifier' => ':identifier',
                    'layout_id' => ':layout_id',
                    'status' => ':status',
                )
            )
            ->setParameter('identifier', $parameters['identifier'], Type::STRING)
            ->setParameter('layout_id', $parameters['layout_id'], Type::INTEGER)
            ->setParameter('status', $parameters['status'], Type::INTEGER);
    }

    /**
     * Builds and returns a block database INSERT query.
     *
     * @param array $parameters
     * @param int $blockId
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function createBlockInsertQuery(array $parameters, $blockId = null)
    {
        return $this->connection->createQueryBuilder()
            ->insert('ngbm_block')
            ->values(
                array(
                    'id' => ':id',
                    'layout_id' => ':layout_id',
                    'zone_identifier' => ':zone_identifier',
                    'position' => ':position',
                    'definition_identifier' => ':definition_identifier',
                    'view_type' => ':view_type',
                    'name' => ':name',
                    'parameters' => ':parameters',
                    'status' => ':status',
                )
            )
            ->setValue(
                'id',
                $blockId !== null ? (int)$blockId : $this->connectionHelper->getAutoIncrementValue('ngbm_block')
            )
            ->setParameter('layout_id', $parameters['layout_id'], Type::INTEGER)
            ->setParameter('zone_identifier', $parameters['zone_identifier'], Type::STRING)
            ->setParameter('position', $parameters['position'], Type::INTEGER)
            ->setParameter('definition_identifier', $parameters['definition_identifier'], Type::STRING)
            ->setParameter('view_type', $parameters['view_type'], Type::STRING)
            ->setParameter('name', trim($parameters['name']), Type::STRING)
            ->setParameter('parameters', $parameters['parameters'], Type::JSON_ARRAY)
            ->setParameter('status', $parameters['status'], Type::INTEGER);
    }

    /**
     * Increments all block positions in a zone starting from provided position.
     *
     * @param int $layoutId
     * @param string $zoneIdentifier
     * @param int $status
     * @param int $startPosition
     * @param int $endPosition
     */
    protected function incrementBlockPositions($layoutId, $zoneIdentifier, $status, $startPosition = null, $endPosition = null)
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->update('ngbm_block')
            ->set('position', 'position + 1')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('layout_id', ':layout_id'),
                    $query->expr()->eq('zone_identifier', ':zone_identifier')
                )
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER)
            ->setParameter('zone_identifier', $zoneIdentifier, Type::STRING);

        if ($startPosition !== null) {
            $query->andWhere($query->expr()->gte('position', ':start_position'));
            $query->setParameter('start_position', $startPosition, Type::INTEGER);
        }

        if ($endPosition !== null) {
            $query->andWhere($query->expr()->lte('position', ':end_position'));
            $query->setParameter('end_position', $endPosition, Type::INTEGER);
        }

        $this->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Decrements all block positions in a zone starting from provided position.
     *
     * @param int $layoutId
     * @param string $zoneIdentifier
     * @param int $status
     * @param int $startPosition
     * @param int $endPosition
     */
    protected function decrementBlockPositions($layoutId, $zoneIdentifier, $status, $startPosition = null, $endPosition = null)
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->update('ngbm_block')
            ->set('position', 'position - 1')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('layout_id', ':layout_id'),
                    $query->expr()->eq('zone_identifier', ':zone_identifier')
                )
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER)
            ->setParameter('zone_identifier', $zoneIdentifier, Type::STRING);

        if ($startPosition !== null) {
            $query->andWhere($query->expr()->gte('position', ':start_position'));
            $query->setParameter('start_position', $startPosition, Type::INTEGER);
        }

        if ($endPosition !== null) {
            $query->andWhere($query->expr()->lte('position', ':end_position'));
            $query->setParameter('end_position', $endPosition, Type::INTEGER);
        }

        $this->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Returns the next available block position for provided zone.
     *
     * @param int $layoutId
     * @param string $zoneIdentifier
     * @param int $status
     *
     * @return int
     */
    protected function getNextBlockPosition($layoutId, $zoneIdentifier, $status)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select($this->connection->getDatabasePlatform()->getMaxExpression('position') . ' AS position')
            ->from('ngbm_block')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('layout_id', ':layout_id'),
                    $query->expr()->eq('zone_identifier', ':zone_identifier')
                )
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER)
            ->setParameter('zone_identifier', $zoneIdentifier, Type::STRING);

        $this->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();

        return isset($data[0]['position']) ? (int)$data[0]['position'] + 1 : 0;
    }
}
