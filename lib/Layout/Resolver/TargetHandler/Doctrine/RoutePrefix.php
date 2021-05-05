<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\TargetHandler\Doctrine;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\TargetHandlerInterface;

final class RoutePrefix implements TargetHandlerInterface
{
    public function handleQuery(QueryBuilder $query, $value): void
    {
        $databasePlatform = $query->getConnection()->getDatabasePlatform();

        $query->andWhere(
            $query->expr()->and(
                $query->expr()->gt(
                    $databasePlatform->getLengthExpression(
                        $databasePlatform->getTrimExpression('rt.value'),
                    ),
                    0,
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
        ->setParameter('target_value', $value, Types::STRING);
    }
}
