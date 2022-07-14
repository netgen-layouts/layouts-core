<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\Helper;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;
use Netgen\Layouts\Exception\BadStateException;

use function is_int;

final class PositionHelper
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Processes the database table to create space for an item which will
     * be inserted at specified position.
     *
     * @param array<string, mixed> $conditions
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If position is out of range
     */
    public function createPosition(array $conditions, ?int $position = null, ?int $endPosition = null, bool $allowOutOfRange = false): int
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

        if ($endPosition !== null && $endPosition < $position) {
            throw new BadStateException('position', 'When creating a position, end position needs to be greater or equal than start position.');
        }

        $this->incrementPositions(
            $conditions,
            $position,
            $endPosition,
        );

        return $position;
    }

    /**
     * Processes the database table to make space for the item which
     * will be moved inside the table.
     *
     * @param array<string, mixed> $conditions
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If position is out of range
     */
    public function moveToPosition(array $conditions, int $originalPosition, int $position, bool $allowOutOfRange = false): int
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
                $position,
            );
        } elseif ($position < $originalPosition) {
            $this->incrementPositions(
                $conditions,
                $position,
                $originalPosition - 1,
            );
        }

        return $position;
    }

    /**
     * Reorders the positions after item at the specified position has been removed.
     *
     * @param array<string, mixed> $conditions
     */
    public function removePosition(array $conditions, int $removedPosition): void
    {
        $this->decrementPositions(
            $conditions,
            $removedPosition + 1,
        );
    }

    /**
     * Returns the next available position in the table.
     *
     * @param array<string, mixed> $conditions
     */
    public function getNextPosition(array $conditions): int
    {
        $columnName = $conditions['column'];

        $query = $this->connection->createQueryBuilder();
        $query->select($this->connection->getDatabasePlatform()->getMaxExpression($columnName) . ' AS ' . $columnName)
            ->from($conditions['table']);

        $this->applyConditions($query, $conditions['conditions']);

        $data = $query->execute()->fetchAllAssociative();

        return (int) ($data[0][$columnName] ?? -1) + 1;
    }

    /**
     * Increments all positions in a table starting from provided position.
     *
     * @param array<string, mixed> $conditions
     */
    private function incrementPositions(array $conditions, ?int $startPosition = null, ?int $endPosition = null): void
    {
        $columnName = $conditions['column'];

        $query = $this->connection->createQueryBuilder();

        $query
            ->update($conditions['table'])
            ->set($columnName, $columnName . ' + 1');

        if ($startPosition !== null) {
            $query->andWhere($query->expr()->gte($columnName, ':start_position'));
            $query->setParameter('start_position', $startPosition, Types::INTEGER);
        }

        if ($endPosition !== null) {
            $query->andWhere($query->expr()->lte($columnName, ':end_position'));
            $query->setParameter('end_position', $endPosition, Types::INTEGER);
        }

        $this->applyConditions($query, $conditions['conditions']);

        $query->execute();
    }

    /**
     * Decrements all positions in a table starting from provided position.
     *
     * @param array<string, mixed> $conditions
     */
    private function decrementPositions(array $conditions, ?int $startPosition = null, ?int $endPosition = null): void
    {
        $columnName = $conditions['column'];

        $query = $this->connection->createQueryBuilder();

        $query
            ->update($conditions['table'])
            ->set($columnName, $columnName . ' - 1');

        if ($startPosition !== null) {
            $query->andWhere($query->expr()->gte($columnName, ':start_position'));
            $query->setParameter('start_position', $startPosition, Types::INTEGER);
        }

        if ($endPosition !== null) {
            $query->andWhere($query->expr()->lte($columnName, ':end_position'));
            $query->setParameter('end_position', $endPosition, Types::INTEGER);
        }

        $this->applyConditions($query, $conditions['conditions']);

        $query->execute();
    }

    /**
     * Applies the provided conditions to the query.
     *
     * @param array<string, mixed> $conditions
     */
    private function applyConditions(QueryBuilder $query, array $conditions): void
    {
        foreach ($conditions as $identifier => $value) {
            $query->andWhere(
                $query->expr()->eq($identifier, ':' . $identifier),
            );

            $query->setParameter($identifier, $value, is_int($value) ? Types::INTEGER : Types::STRING);
        }
    }
}
