<?php

namespace Netgen\BlockManager\Core\Persistence\Doctrine\Block;

use Netgen\BlockManager\Core\Persistence\Doctrine\Connection\Helper;
use Netgen\BlockManager\Persistence\Handler\Block as BlockHandlerInterface;
use Netgen\BlockManager\API\Values\BlockCreateStruct;
use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\API\Exception\NotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

class Handler implements BlockHandlerInterface
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
     * @var \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Mapper
     */
    protected $mapper;

    /**
     * Constructor.
     *
     * @param \Doctrine\DBAL\Connection $connection
     * @param \Netgen\BlockManager\Core\Persistence\Doctrine\Connection\Helper $connectionHelper
     * @param \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Mapper $mapper
     */
    public function __construct(Connection $connection, Helper $connectionHelper, Mapper $mapper)
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
    public function loadBlock($blockId, $status = Layout::STATUS_PUBLISHED)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('id', 'zone_id', 'definition_identifier', 'view_type', 'name', 'parameters', 'status')
            ->from('ngbm_block')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('id', ':block_id'),
                    $query->expr()->eq('status', ':status')
                )
            )
            ->setParameter('block_id', $blockId, Type::INTEGER)
            ->setParameter('status', $status, Type::INTEGER);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            throw new NotFoundException('block', $blockId);
        }

        $data = $this->mapper->mapBlocks($data);

        return reset($data);
    }

    /**
     * Loads all blocks from zone with specified ID.
     *
     * @param int|string $zoneId
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block[]
     */
    public function loadZoneBlocks($zoneId, $status = Layout::STATUS_PUBLISHED)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('id', 'zone_id', 'definition_identifier', 'view_type', 'name', 'parameters', 'status')
            ->from('ngbm_block')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('zone_id', ':zone_id'),
                    $query->expr()->eq('status', ':status')
                )
            )
            ->setParameter('zone_id', $zoneId, Type::INTEGER)
            ->setParameter('status', $status, Type::INTEGER);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            return array();
        }

        return $this->mapper->mapBlocks($data);
    }

    /**
     * Creates a block in specified zone.
     *
     * @param \Netgen\BlockManager\API\Values\BlockCreateStruct $blockCreateStruct
     * @param int|string $zoneId
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function createBlock(BlockCreateStruct $blockCreateStruct, $zoneId)
    {
        // @TODO: Verify that zone has the same status as the block

        $query = $this->createBlockInsertQuery(
            array(
                'id' => $this->connectionHelper->getAutoIncrementValue('ngbm_block'),
                'zone_id' => $zoneId,
                'definition_identifier' => $blockCreateStruct->definitionIdentifier,
                'view_type' => $blockCreateStruct->viewType,
                'name' => $blockCreateStruct->name,
                'parameters' => $blockCreateStruct->getParameters(),
                'status' => Layout::STATUS_DRAFT,
            )
        );

        $query->execute();

        return $this->loadBlock(
            $this->connectionHelper->lastInsertId('ngbm_block'),
            Layout::STATUS_DRAFT
        );
    }

    /**
     * Updates a block with specified ID.
     *
     * @param int|string $blockId
     * @param \Netgen\BlockManager\API\Values\BlockUpdateStruct $blockUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function updateBlock($blockId, BlockUpdateStruct $blockUpdateStruct)
    {
        $block = $this->loadBlock($blockId, Layout::STATUS_DRAFT);

        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_block')
            ->set('view_type', ':view_type')
            ->set('name', ':name')
            ->set('parameters', ':parameters')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('id', ':block_id'),
                    $query->expr()->eq('status', ':status')
                )
            )
            ->setParameter('block_id', $block->id, Type::INTEGER)
            ->setParameter('view_type', $blockUpdateStruct->viewType, Type::STRING)
            ->setParameter('name', trim($blockUpdateStruct->name), Type::STRING)
            ->setParameter('parameters', $blockUpdateStruct->getParameters(), Type::JSON_ARRAY)
            ->setParameter('status', Layout::STATUS_DRAFT, Type::INTEGER);

        $query->execute();

        return $this->loadBlock($blockId, Layout::STATUS_DRAFT);
    }

    /**
     * Copies a block with specified ID to a zone with specified ID.
     *
     * @param int|string $blockId
     * @param int|string $zoneId
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function copyBlock($blockId, $zoneId = null)
    {
        // @TODO: Verify that zone has the same status as the block

        $originalBlock = $this->loadBlock($blockId, Layout::STATUS_DRAFT);

        $query = $this->createBlockInsertQuery(
            array(
                'id' => $this->connectionHelper->getAutoIncrementValue('ngbm_block'),
                'zone_id' => $zoneId !== null ? $zoneId : $originalBlock->zoneId,
                'definition_identifier' => $originalBlock->definitionIdentifier,
                'view_type' => $originalBlock->viewType,
                'name' => $originalBlock->name,
                'parameters' => $originalBlock->parameters,
                'status' => Layout::STATUS_DRAFT,
            )
        );

        $query->execute();

        return $this->loadBlock(
            $this->connectionHelper->lastInsertId('ngbm_block'),
            Layout::STATUS_DRAFT
        );
    }

    /**
     * Moves a block to zone with specified ID.
     *
     * @param int|string $blockId
     * @param int|string $zoneId
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function moveBlock($blockId, $zoneId)
    {
        // @TODO: Verify that the zone has the same status as the block

        $block = $this->loadBlock($blockId, Layout::STATUS_DRAFT);

        $query = $this->connection->createQueryBuilder();

        $query
            ->update('ngbm_block')
            ->set('zone_id', ':zone_id')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('id', ':block_id'),
                    $query->expr()->eq('status', ':status')
                )
            )
            ->setParameter('block_id', $block->id, Type::INTEGER)
            ->setParameter('zone_id', $zoneId, Type::INTEGER)
            ->setParameter('status', Layout::STATUS_DRAFT, Type::INTEGER);

        $query->execute();

        return $this->loadBlock($blockId, Layout::STATUS_DRAFT);
    }

    /**
     * Deletes a block with specified ID.
     *
     * @param int|string $blockId
     * @param int $status
     */
    public function deleteBlock($blockId, $status = null)
    {
        $query = $this->connection->createQueryBuilder();

        if ($status !== null) {
            $query->delete('ngbm_block')
                ->where(
                    $query->expr()->andX(
                        $query->expr()->eq('id', ':block_id'),
                        $query->expr()->eq('status', ':status')
                    )
                )
                ->setParameter('block_id', $blockId, Type::INTEGER)
                ->setParameter('status', $status, Type::INTEGER);
        } else {
            $query->delete('ngbm_block')
                ->where(
                    $query->expr()->eq('id', ':block_id')
                )
                ->setParameter('block_id', $blockId, Type::INTEGER);
        }

        $query->execute();

        // @TODO: Delete block items
    }

    /**
     * Builds and returns a block database INSERT query.
     *
     * @param array $parameters
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function createBlockInsertQuery(array $parameters)
    {
        return $this->connection->createQueryBuilder()
            ->insert('ngbm_block')
            ->values(
                array(
                    'id' => ':id',
                    'zone_id' => ':zone_id',
                    'definition_identifier' => ':definition_identifier',
                    'view_type' => ':view_type',
                    'name' => ':name',
                    'parameters' => ':parameters',
                    'status' => ':status',
                )
            )
            ->setParameter('id', $parameters['id'], Type::INTEGER)
            ->setParameter('zone_id', $parameters['zone_id'], Type::INTEGER)
            ->setParameter('definition_identifier', $parameters['definition_identifier'], Type::STRING)
            ->setParameter('view_type', $parameters['view_type'], Type::STRING)
            ->setParameter('name', trim($parameters['name']), Type::STRING)
            ->setParameter('parameters', $parameters['parameters'], Type::JSON_ARRAY)
            ->setParameter('status', $parameters['status'], Type::INTEGER);
    }
}
