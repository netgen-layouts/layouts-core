<?php

namespace Netgen\BlockManager\Core\Persistence\Doctrine\Layout;

use Netgen\BlockManager\Persistence\Handler\Layout as LayoutHandlerInterface;
use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Exceptions\NotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

class Handler implements LayoutHandlerInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @var \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Mapper
     */
    protected $mapper;

    /**
     * Constructor.
     *
     * @param \Doctrine\DBAL\Connection $connection
     * @param \Netgen\BlockManager\Core\Persistence\Doctrine\Layout\Mapper $mapper
     */
    public function __construct(Connection $connection, Mapper $mapper)
    {
        $this->connection = $connection;
        $this->mapper = $mapper;
    }

    /**
     * Loads a layout with specified ID.
     *
     * @param int|string $layoutId
     *
     * @throws \Netgen\BlockManager\API\Exceptions\NotFoundException If layout with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function loadLayout($layoutId)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('id', 'parent_id', 'identifier', 'created', 'modified')
            ->from('ngbm_layout')
            ->where(
                $query->expr()->eq('id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            throw new NotFoundException('layout', $layoutId);
        }

        $data = $this->mapper->mapLayouts($data);

        return reset($data);
    }

    /**
     * Loads a layout with specified identifier.
     *
     * @param string $layoutIdentifier
     *
     * @throws \Netgen\BlockManager\API\Exceptions\NotFoundException If layout with specified identifier does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function loadLayoutByIdentifier($layoutIdentifier)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('id', 'parent_id', 'identifier', 'created', 'modified')
            ->from('ngbm_layout')
            ->where(
                $query->expr()->eq('identifier', ':layout_identifier')
            )
            ->setParameter('layout_identifier', $layoutIdentifier, Type::STRING);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            throw new NotFoundException('layout', $layoutIdentifier);
        }

        $data = $this->mapper->mapLayouts($data);

        return reset($data);
    }

    /**
     * Loads a zone with specified ID.
     *
     * @param int|string $zoneId
     *
     * @throws \Netgen\BlockManager\API\Exceptions\NotFoundException If zone with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Zone
     */
    public function loadZone($zoneId)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('id', 'layout_id', 'identifier')
            ->from('ngbm_zone')
            ->where(
                $query->expr()->eq('id', ':zone_id')
            )
            ->setParameter('zone_id', $zoneId, Type::INTEGER);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            throw new NotFoundException('zone', $zoneId);
        }

        $data = $this->mapper->mapZones($data);

        return reset($data);
    }

    /**
     * Loads all zones that belong to layout with specified ID.
     *
     * @param int|string $layoutId
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Zone[]
     */
    public function loadLayoutZones($layoutId)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('id', 'layout_id', 'identifier')
            ->from('ngbm_zone')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER);

        $data = $query->execute()->fetchAll();
        if (empty($data)) {
            return array();
        }

        return $this->mapper->mapZones($data);
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
                'identifier' => $layoutCreateStruct->layoutIdentifier,
                'created' => $currentTimeStamp,
                'modified' => $currentTimeStamp,
            )
        );

        $query->execute();

        $createdLayoutId = (int)$this->connection->lastInsertId();
        foreach ($layoutCreateStruct->zoneIdentifiers as $zoneIdentifier) {
            $zoneQuery = $this->createZoneInsertQuery(
                array(
                    'layout_id' => $createdLayoutId,
                    'identifier' => $zoneIdentifier,
                )
            );

            $zoneQuery->execute();
        }

        return $this->loadLayout($createdLayoutId);
    }

    /**
     * Copies a layout with specified ID.
     *
     * @param int|string $layoutId
     * @param string $newLayoutIdentifier
     *
     * @return \Netgen\BlockManager\Persistence\Values\Page\Layout
     */
    public function copyLayout($layoutId, $newLayoutIdentifier)
    {
        $originalLayout = $this->loadLayout($layoutId);
        $originalZones = $this->loadLayoutZones($layoutId);

        $currentTimeStamp = time();

        $query = $this->createLayoutInsertQuery(
            array(
                'parent_id' => $originalLayout->parentId,
                'identifier' => $newLayoutIdentifier,
                'created' => $currentTimeStamp,
                'modified' => $currentTimeStamp,
            )
        );

        $query->execute();

        $copiedLayoutId = (int)$this->connection->lastInsertId();
        foreach ($originalZones as $originalZone) {
            $zoneQuery = $this->createZoneInsertQuery(
                array(
                    'layout_id' => $copiedLayoutId,
                    'identifier' => $originalZone->identifier,
                )
            );

            $zoneQuery->execute();
        }

        return $this->loadLayout($copiedLayoutId);
    }

    /**
     * Deletes a layout with specified ID.
     *
     * @param int|string $layoutId
     */
    public function deleteLayout($layoutId)
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_zone')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER);

        $query->execute();

        $query->delete('ngbm_layout')
            ->where(
                $query->expr()->eq('id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER);

        $query->execute();
    }

    /**
     * Builds and returns a layout database INSERT query.
     *
     * @param array $parameters
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function createLayoutInsertQuery(array $parameters)
    {
        return $this->connection->createQueryBuilder()
            ->insert('ngbm_layout')
            ->values(
                array(
                    'parent_id' => ':parent_id',
                    'identifier' => ':identifier',
                    'created' => ':created',
                    'modified' => ':modified',
                )
            )
            ->setParameter('parent_id', $parameters['parent_id'], Type::INTEGER)
            ->setParameter('identifier', $parameters['identifier'], Type::STRING)
            ->setParameter('created', $parameters['created'], Type::INTEGER)
            ->setParameter('modified', $parameters['modified'], Type::INTEGER);
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
                    'layout_id' => ':layout_id',
                    'identifier' => ':identifier',
                )
            )
            ->setParameter('layout_id', $parameters['layout_id'], Type::INTEGER)
            ->setParameter('identifier', $parameters['identifier'], Type::STRING);
    }
}
