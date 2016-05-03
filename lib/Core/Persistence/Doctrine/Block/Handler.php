<?php

namespace Netgen\BlockManager\Core\Persistence\Doctrine\Block;

use Netgen\BlockManager\API\Values\BlockCreateStruct;
use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use Netgen\BlockManager\Core\Persistence\Doctrine\Helpers\ConnectionHelper;
use Netgen\BlockManager\Persistence\Handler\Block as BlockHandlerInterface;
use Netgen\BlockManager\API\Exception\NotFoundException;
use Netgen\BlockManager\API\Exception\BadStateException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

class Handler implements BlockHandlerInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @var \Netgen\BlockManager\Core\Persistence\Doctrine\Helpers\ConnectionHelper
     */
    protected $connectionHelper;

    /**
     * @var \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Mapper
     */
    protected $mapper;

    /**
     * Constructor.
     *
     * @param \Doctrine\DBAL\Connection $connection
     * @param \Netgen\BlockManager\Core\Persistence\Doctrine\Helpers\ConnectionHelper $connectionHelper
     * @param \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Mapper $mapper
     */
    public function __construct(Connection $connection, ConnectionHelper $connectionHelper, Mapper $mapper)
    {
        $this->connection = $connection;
        $this->connectionHelper = $connectionHelper;
        $this->mapper = $mapper;
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

        $this->connectionHelper->applyStatusCondition($query, $status);

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

        $this->connectionHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            return array();
        }

        return $this->mapper->mapBlocks($data);
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
        $nextBlockPosition = $this->getNextBlockPosition(
            $layoutId,
            $zoneIdentifier,
            $status
        );

        if ($position !== null) {
            if ($position > $nextBlockPosition || $position < 0) {
                throw new BadStateException('position', 'Position is out of range.');
            }
        } else {
            $position = $nextBlockPosition;
        }

        $this->incrementBlockPositions(
            $layoutId,
            $zoneIdentifier,
            $status,
            $position
        );

        $query = $this->createBlockInsertQuery(
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
            ->setParameter('view_type', $blockUpdateStruct->viewType !== null ? $blockUpdateStruct->viewType : $block->viewType, Type::STRING)
            ->setParameter('name', $blockUpdateStruct->name !== null ? trim($blockUpdateStruct->name) : $block->name, Type::STRING)
            ->setParameter('parameters', $blockUpdateStruct->getParameters(), Type::JSON_ARRAY);

        $this->connectionHelper->applyStatusCondition($query, $status);

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

        $query = $this->createBlockInsertQuery(
            array(
                'status' => $block->status,
                'layout_id' => $block->layoutId,
                'zone_identifier' => $zoneIdentifier,
                'position' => $this->getNextBlockPosition($block->layoutId, $zoneIdentifier, $status),
                'definition_identifier' => $block->definitionIdentifier,
                'view_type' => $block->viewType,
                'name' => $block->name,
                'parameters' => $block->parameters,
            )
        );

        $query->execute();

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
     * @throws \Netgen\BlockManager\API\Exception\BadStateException If provided position is out of range
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function moveBlock($blockId, $status, $position)
    {
        $block = $this->loadBlock($blockId, $status);

        $nextBlockPosition = $this->getNextBlockPosition(
            $block->layoutId,
            $block->zoneIdentifier,
            $status
        );

        if ($position >= $nextBlockPosition || $position < 0) {
            throw new BadStateException('position', 'Position is out of range.');
        }

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

        $this->connectionHelper->applyStatusCondition($query, $status);

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

        $nextBlockPosition = $this->getNextBlockPosition(
            $block->layoutId,
            $zoneIdentifier,
            $status
        );

        if ($position > $nextBlockPosition || $position < 0) {
            throw new BadStateException('position', 'Position is out of range.');
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

        $this->connectionHelper->applyStatusCondition($query, $status);

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
     * Deletes a block with specified ID.
     *
     * @param int|string $blockId
     * @param int $status
     */
    public function deleteBlock($blockId, $status)
    {
        $block = $this->loadBlock($blockId, $status);

        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_block')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $blockId, Type::INTEGER);

        $this->connectionHelper->applyStatusCondition($query, $status);

        $query->execute();

        $this->decrementBlockPositions(
            $block->layoutId,
            $block->zoneIdentifier,
            $status,
            $block->position
        );
    }

    /**
     * Builds and returns a block database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function createBlockSelectQuery()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('id', 'status', 'layout_id', 'zone_identifier', 'position', 'definition_identifier', 'view_type', 'name', 'parameters')
            ->from('ngbm_block');

        return $query;
    }

    /**
     * Builds and returns a block database INSERT query.
     *
     * @param array $parameters
     * @param int $blockId
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function createBlockInsertQuery(array $parameters, $blockId = null)
    {
        return $this->connection->createQueryBuilder()
            ->insert('ngbm_block')
            ->values(
                array(
                    'id' => ':id',
                    'status' => ':status',
                    'layout_id' => ':layout_id',
                    'zone_identifier' => ':zone_identifier',
                    'position' => ':position',
                    'definition_identifier' => ':definition_identifier',
                    'view_type' => ':view_type',
                    'name' => ':name',
                    'parameters' => ':parameters',
                )
            )
            ->setValue(
                'id',
                $blockId !== null ? (int)$blockId : $this->connectionHelper->getAutoIncrementValue('ngbm_block')
            )
            ->setParameter('status', $parameters['status'], Type::INTEGER)
            ->setParameter('layout_id', $parameters['layout_id'], Type::INTEGER)
            ->setParameter('zone_identifier', $parameters['zone_identifier'], Type::STRING)
            ->setParameter('position', $parameters['position'], Type::INTEGER)
            ->setParameter('definition_identifier', $parameters['definition_identifier'], Type::STRING)
            ->setParameter('view_type', $parameters['view_type'], Type::STRING)
            ->setParameter('name', trim($parameters['name']), Type::STRING)
            ->setParameter('parameters', $parameters['parameters'], Type::JSON_ARRAY);
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
    public function incrementBlockPositions($layoutId, $zoneIdentifier, $status, $startPosition = null, $endPosition = null)
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

        $this->connectionHelper->applyStatusCondition($query, $status);

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
    public function decrementBlockPositions($layoutId, $zoneIdentifier, $status, $startPosition = null, $endPosition = null)
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

        $this->connectionHelper->applyStatusCondition($query, $status);

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
    public function getNextBlockPosition($layoutId, $zoneIdentifier, $status)
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

        $this->connectionHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();

        return isset($data[0]['position']) ? (int)$data[0]['position'] + 1 : 0;
    }
}
