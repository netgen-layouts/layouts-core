<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Result;

use Iterator;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Item\CmsItemBuilderInterface;

final class QueryRunner implements QueryRunnerInterface
{
    public function __construct(
        private CmsItemBuilderInterface $cmsItemBuilder,
    ) {}

    public function runQuery(Query $query, int $offset = 0, ?int $limit = null): Iterator
    {
        $queryValues = $query->queryType->getValues($query, $offset, $limit);

        foreach ($queryValues as $queryValue) {
            yield $this->cmsItemBuilder->build($queryValue);
        }
    }

    public function count(Query $query): int
    {
        return $query->queryType->getCount($query);
    }
}
