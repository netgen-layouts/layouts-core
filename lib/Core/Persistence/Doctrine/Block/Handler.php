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
        $query->select('id', 'layout_id', 'zone_identifier', 'definition_identifier', 'view_type', 'name', 'parameters', 'status')
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
     * Loads all blocks from zone with specified identifier.
     *
     * @param int|string $layoutId
     * @param string $zoneIdentifier
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block[]
     */
    public function loadZoneBlocks($layoutId, $zoneIdentifier, $status = Layout::STATUS_PUBLISHED)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('id', 'layout_id', 'zone_identifier', 'definition_identifier', 'view_type', 'name', 'parameters', 'status')
            ->from('ngbm_block')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('layout_id', ':layout_id'),
                    $query->expr()->eq('zone_identifier', ':zone_identifier'),
                    $query->expr()->eq('status', ':status')
                )
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER)
            ->setParameter('zone_identifier', $zoneIdentifier, Type::STRING)
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
     * @param int|string $layoutId
     * @param string $zoneIdentifier
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function createBlock(BlockCreateStruct $blockCreateStruct, $layoutId, $zoneIdentifier)
    {
        $query = $this->createBlockInsertQuery(
            array(
                'id' => $this->connectionHelper->getAutoIncrementValue('ngbm_block'),
                'layout_id' => $layoutId,
                'zone_identifier' => $zoneIdentifier,
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
     * Copies a block with specified ID to a zone with specified identifier.
     *
     * @param int|string $blockId
     * @param int|string $layoutId
     * @param string $zoneIdentifier
     * @param bool $createNew
     * @param int $status
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function copyBlock($blockId, $layoutId = null, $zoneIdentifier = null, $createNew = true, $status = Layout::STATUS_PUBLISHED, $newStatus = Layout::STATUS_DRAFT)
    {
        // @TODO: Verify that layout has the same status as the block

        $originalBlock = $this->loadBlock($blockId, $status);

        $newBlockId = $createNew ?
            $this->connectionHelper->getAutoIncrementValue('ngbm_block') :
            $blockId;

        $query = $this->createBlockInsertQuery(
            array(
                'id' => $newBlockId,
                'layout_id' => $layoutId !== null ? $layoutId : $originalBlock->layoutId,
                'zone_identifier' => $zoneIdentifier !== null ? $zoneIdentifier : $originalBlock->zoneIdentifier,
                'definition_identifier' => $originalBlock->definitionIdentifier,
                'view_type' => $originalBlock->viewType,
                'name' => $originalBlock->name,
                'parameters' => $originalBlock->parameters,
                'status' => $newStatus,
            )
        );

        $query->execute();

        // @TODO: Copy block items

        $newBlockId = $createNew ?
            (int)$this->connectionHelper->lastInsertId('ngbm_block') :
            $blockId;

        return $this->loadBlock(
            $newBlockId,
            $newStatus
        );
    }

    /**
     * Moves a block to zone with specified identifier.
     *
     * @param int|string $blockId
     * @param string $zoneIdentifier
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function moveBlock($blockId, $zoneIdentifier)
    {
        $block = $this->loadBlock($blockId, Layout::STATUS_DRAFT);

        $query = $this->connection->createQueryBuilder();

        $query
            ->update('ngbm_block')
            ->set('zone_identifier', ':zone_identifier')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('id', ':block_id'),
                    $query->expr()->eq('status', ':status')
                )
            )
            ->setParameter('block_id', $block->id, Type::INTEGER)
            ->setParameter('zone_identifier', $zoneIdentifier, Type::STRING)
            ->setParameter('status', Layout::STATUS_DRAFT, Type::INTEGER);

        $query->execute();

        return $this->loadBlock($blockId, Layout::STATUS_DRAFT);
    }

    /**
     * Deletes a block with specified ID.
     *
     * @param int|string $blockId
     */
    public function deleteBlock($blockId)
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_block')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('id', ':block_id'),
                    $query->expr()->eq('status', ':status')
                )
            )
            ->setParameter('block_id', $blockId, Type::INTEGER)
            ->setParameter('status', Layout::STATUS_DRAFT, Type::INTEGER);

        $query->execute();

        // @TODO: Delete block items
    }

    /**
     * Deletes all blocks within the specified layout.
     *
     * @param int|string $layoutId
     * @param int $status
     */
    public function deleteLayoutBlocks($layoutId, $status = null)
    {
        $query = $this->connection->createQueryBuilder();

        if ($status !== null) {
            $query->delete('ngbm_block')
                ->where(
                    $query->expr()->andX(
                        $query->expr()->in('layout_id', ':layout_id'),
                        $query->expr()->eq('status', ':status')
                    )
                )
                ->setParameter('layout_id', $layoutId, Type::INTEGER)
                ->setParameter('status', $status, Type::INTEGER);
        } else {
            $query->delete('ngbm_block')
                ->where(
                    $query->expr()->in('layout_id', ':layout_id')
                )
                ->setParameter('layout_id', $layoutId, Type::INTEGER);
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
                    'layout_id' => ':layout_id',
                    'zone_identifier' => ':zone_identifier',
                    'definition_identifier' => ':definition_identifier',
                    'view_type' => ':view_type',
                    'name' => ':name',
                    'parameters' => ':parameters',
                    'status' => ':status',
                )
            )
            ->setParameter('id', $parameters['id'], Type::INTEGER)
            ->setParameter('layout_id', $parameters['layout_id'], Type::INTEGER)
            ->setParameter('zone_identifier', $parameters['zone_identifier'], Type::STRING)
            ->setParameter('definition_identifier', $parameters['definition_identifier'], Type::STRING)
            ->setParameter('view_type', $parameters['view_type'], Type::STRING)
            ->setParameter('name', trim($parameters['name']), Type::STRING)
            ->setParameter('parameters', $parameters['parameters'], Type::JSON_ARRAY)
            ->setParameter('status', $parameters['status'], Type::INTEGER);
    }
}
