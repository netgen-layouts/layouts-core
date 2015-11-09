<?php

namespace Netgen\BlockManager\Core\Persistence\Doctrine\Block;

use Netgen\BlockManager\Persistence\Handler\Block as BlockHandlerInterface;
use Netgen\BlockManager\API\Values\BlockCreateStruct;
use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use Netgen\BlockManager\Exceptions\NotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

class Handler implements BlockHandlerInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @var \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Mapper
     */
    protected $mapper;

    /**
     * Constructor.
     *
     * @param \Doctrine\DBAL\Connection $connection
     * @param \Netgen\BlockManager\Core\Persistence\Doctrine\Block\Mapper $mapper
     */
    public function __construct(Connection $connection, Mapper $mapper)
    {
        $this->connection = $connection;
        $this->mapper = $mapper;
    }

    /**
     * Loads a block with specified ID.
     *
     * @param int|string $blockId
     *
     * @throws \Netgen\BlockManager\Exceptions\NotFoundException If block with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function loadBlock($blockId)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('id', 'zone_id', 'definition_identifier', 'view_type', 'parameters')
            ->from('ngbm_block')
            ->where(
                $query->expr()->eq('id', ':block_id')
            )
            ->setParameter('block_id', $blockId, Type::INTEGER);

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
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block[]
     */
    public function loadZoneBlocks($zoneId)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('id', 'zone_id', 'definition_identifier', 'view_type', 'parameters')
            ->from('ngbm_block')
            ->where(
                $query->expr()->eq('zone_id', ':zone_id')
            )
            ->setParameter('zone_id', $zoneId, Type::INTEGER);

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
        $query = $this->createBlockInsertQuery(
            array(
                'zone_id' => $zoneId,
                'definition_identifier' => $blockCreateStruct->definitionIdentifier,
                'view_type' => $blockCreateStruct->viewType,
                'parameters' => $blockCreateStruct->getParameters(),
            )
        );

        $query->execute();

        return $this->loadBlock($this->connection->lastInsertId());
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
        $block = $this->loadBlock($blockId);
        $blockParameters = array_merge($block->parameters, $blockUpdateStruct->getParameters());

        $query = $this->connection->createQueryBuilder();

        $query
            ->update('ngbm_block')
            ->set('view_type', ':view_type')
            ->set('parameters', ':parameters')
            ->where(
                $query->expr()->eq('id', ':block_id')
            )
            ->setParameter('block_id', $block->id, Type::INTEGER)
            ->setParameter(
                'view_type',
                $blockUpdateStruct->viewType !== null ?
                    $blockUpdateStruct->viewType :
                    $block->viewType,
                Type::STRING
            )
            ->setParameter('parameters', $blockParameters, Type::JSON_ARRAY);

        $query->execute();

        return $this->loadBlock($blockId);
    }

    /**
     * Copies a block with specified ID to a zone with specified ID.
     *
     * @param int|string $blockId
     * @param int|string $zoneId
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Block
     */
    public function copyBlock($blockId, $zoneId)
    {
        $originalBlock = $this->loadBlock($blockId);

        $query = $this->createBlockInsertQuery(
            array(
                'zone_id' => $zoneId,
                'definition_identifier' => $originalBlock->definitionIdentifier,
                'view_type' => $originalBlock->viewType,
                'parameters' => $originalBlock->parameters,
            )
        );

        $query->execute();

        return $this->loadBlock($this->connection->lastInsertId());
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
        $query = $this->connection->createQueryBuilder();

        $query
            ->update('ngbm_block')
            ->set('zone_id', ':zone_id')
            ->where(
                $query->expr()->eq('id', ':block_id')
            )
            ->setParameter('block_id', $blockId, Type::INTEGER)
            ->setParameter('zone_id', $zoneId, Type::INTEGER);

        $query->execute();

        return $this->loadBlock($blockId);
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
                $query->expr()->eq('id', ':block_id')
            )
            ->setParameter('block_id', $blockId, Type::INTEGER);

        $query->execute();
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
                    'zone_id' => ':zone_id',
                    'definition_identifier' => ':definition_identifier',
                    'view_type' => ':view_type',
                    'parameters' => ':parameters',
                )
            )
            ->setParameter('zone_id', $parameters['zone_id'], Type::INTEGER)
            ->setParameter('definition_identifier', $parameters['definition_identifier'], Type::STRING)
            ->setParameter('view_type', $parameters['view_type'], Type::STRING)
            ->setParameter('parameters', $parameters['parameters'], Type::JSON_ARRAY);
    }
}
