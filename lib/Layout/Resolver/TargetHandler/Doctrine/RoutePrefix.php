<?php

namespace Netgen\BlockManager\Layout\Resolver\TargetHandler\Doctrine;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Type;

final class RoutePrefix implements TargetHandlerInterface
{
    public function handleQuery(QueryBuilder $query, $value)
    {
        $databasePlatform = $query->getConnection()->getDatabasePlatform();

        $query->andWhere(
            $query->expr()->andX(
                $query->expr()->gt(
                    $databasePlatform->getLengthExpression(
                        $databasePlatform->getTrimExpression('rt.value')
                    ),
                    0
                ),
                $query->expr()->like(
                    ':target_value',
                    $databasePlatform->getConcatExpression(
                        $databasePlatform->getTrimExpression('rt.value'),
                        "'%'"
                    )
                )
            )
        )
        ->setParameter(':target_value', $value, Type::STRING);

        return $query;
    }
}
