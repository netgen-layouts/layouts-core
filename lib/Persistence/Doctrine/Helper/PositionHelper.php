<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Helper;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Type;
use Netgen\BlockManager\Exception\BadStateException;

final class PositionHelper
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Processes the database table to create space for an item which will
     * be inserted at specified position.
     *
     * @param array $conditions
     * @param int $position
     * @param bool $allowOutOfRange
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If position is out of range
     *
     * @return int
     */
    public function createPosition(array $conditions, $position = null, $allowOutOfRange = false)
    {
        $nextPosition = $this->getNextPosition($conditions);

        if ($position === null) {
            return $nextPosition;
        }

        if ($position < 0) {
            throw new BadStateException('position', 'Position cannot be negative.');
        }

        if (!$allowOutOfRange && $position > $nextPosition) {
            throw new BadStateException('position', 'Position is out of range.');
        }

        $this->incrementPositions(
            $conditions,
            $position
        );

        return $position;
    }

    /**
     * Processes the database table to make space for the item which
     * will be moved inside the table.
     *
     * @param array $conditions
     * @param int $originalPosition
     * @param int $position
     * @param bool $allowOutOfRange
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If position is out of range
     *
     * @return int
     */
    public function moveToPosition(array $conditions, $originalPosition, $position, $allowOutOfRange = false)
    {
        $nextPosition = $this->getNextPosition($conditions);

        if ($position < 0) {
            throw new BadStateException('position', 'Position cannot be negative.');
        }

        if (!$allowOutOfRange && $position >= $nextPosition) {
            throw new BadStateException('position', 'Position is out of range.');
        }

        if ($position > $originalPosition) {
            $this->decrementPositions(
                $conditions,
                $originalPosition + 1,
                $position
            );
        } elseif ($position < $originalPosition) {
            $this->incrementPositions(
                $conditions,
                $position,
                $originalPosition - 1
            );
        }

        return $position;
    }

    /**
     * Reorders the positions after item at the specified position has been removed.
     *
     * @param array $conditions
     * @param int $removedPosition
     */
    public function removePosition(array $conditions, $removedPosition)
    {
        $this->decrementPositions(
            $conditions,
            $removedPosition + 1
        );
    }

    /**
     * Returns the next available position in the table.
     *
     * @param array $conditions
     *
     * @return int
     */
    public function getNextPosition(array $conditions)
    {
        $columnName = $conditions['column'];

        $query = $this->connection->createQueryBuilder();
        $query->select($this->connection->getDatabasePlatform()->getMaxExpression($columnName) . ' AS ' . $columnName)
            ->from($conditions['table']);

        $this->applyConditions($query, $conditions['conditions']);

        $data = $query->execute()->fetchAll();

        return isset($data[0][$columnName]) ? (int) $data[0][$columnName] + 1 : 0;
    }

    /**
     * Increments all positions in a table starting from provided position.
     *
     * @param array $conditions
     * @param int $startPosition
     * @param int $endPosition
     */
    private function incrementPositions(array $conditions, $startPosition = null, $endPosition = null)
    {
        $columnName = $conditions['column'];

        $query = $this->connection->createQueryBuilder();

        $query
            ->update($conditions['table'])
            ->set($columnName, $columnName . ' + 1');

        if ($startPosition !== null) {
            $query->andWhere($query->expr()->gte($columnName, ':start_position'));
            $query->setParameter('start_position', $startPosition, Type::INTEGER);
        }

        if ($endPosition !== null) {
            $query->andWhere($query->expr()->lte($columnName, ':end_position'));
            $query->setParameter('end_position', $endPosition, Type::INTEGER);
        }

        $this->applyConditions($query, $conditions['conditions']);

        $query->execute();
    }

    /**
     * Decrements all positions in a table starting from provided position.
     *
     * @param array $conditions
     * @param int $startPosition
     * @param int $endPosition
     */
    private function decrementPositions(array $conditions, $startPosition = null, $endPosition = null)
    {
        $columnName = $conditions['column'];

        $query = $this->connection->createQueryBuilder();

        $query
            ->update($conditions['table'])
            ->set($columnName, $columnName . ' - 1');

        if ($startPosition !== null) {
            $query->andWhere($query->expr()->gte($columnName, ':start_position'));
            $query->setParameter('start_position', $startPosition, Type::INTEGER);
        }

        if ($endPosition !== null) {
            $query->andWhere($query->expr()->lte($columnName, ':end_position'));
            $query->setParameter('end_position', $endPosition, Type::INTEGER);
        }

        $this->applyConditions($query, $conditions['conditions']);

        $query->execute();
    }

    /**
     * Applies the provided conditions to the query.
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query
     * @param array $conditions
     */
    private function applyConditions(QueryBuilder $query, array $conditions)
    {
        foreach ($conditions as $identifier => $value) {
            $query->andWhere(
                $query->expr()->eq($identifier, ':' . $identifier)
            );

            $query->setParameter($identifier, $value, is_int($value) ? Type::INTEGER : Type::STRING);
        }
    }
}
