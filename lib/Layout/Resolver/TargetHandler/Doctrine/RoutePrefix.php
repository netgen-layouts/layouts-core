<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\TargetHandler\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\TargetHandlerInterface;

final class RoutePrefix implements TargetHandlerInterface
{
    public function __construct(
        private Connection $connection,
    ) {}

    public function handleQuery(QueryBuilder $query, mixed $value): void
    {
        $databasePlatform = $this->connection->getDatabasePlatform();

        $query->andWhere(
            $query->expr()->and(
                $query->expr()->gt(
                    $databasePlatform->getLengthExpression(
                        $databasePlatform->getTrimExpression('rt.value'),
                    ),
                    ':target_value_length',
                ),
                $query->expr()->like(
                    ':target_value',
                    $databasePlatform->getConcatExpression(
                        $databasePlatform->getTrimExpression('rt.value'),
                        "'%'",
                    ),
                ),
            ),
        )
        ->setParameter('target_value_length', 0, Types::INTEGER)
        ->setParameter('target_value', $value, Types::STRING);
    }
}
