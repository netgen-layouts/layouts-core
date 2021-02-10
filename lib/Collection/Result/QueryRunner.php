<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Result;

use Iterator;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Item\CmsItemBuilderInterface;

final class QueryRunner implements QueryRunnerInterface
{
    private CmsItemBuilderInterface $cmsItemBuilder;

    public function __construct(CmsItemBuilderInterface $cmsItemBuilder)
    {
        $this->cmsItemBuilder = $cmsItemBuilder;
    }

    public function runQuery(Query $query, int $offset = 0, ?int $limit = null): Iterator
    {
        $queryValues = $query->getQueryType()->getValues($query, $offset, $limit);

        foreach ($queryValues as $queryValue) {
            yield $this->cmsItemBuilder->build($queryValue);
        }
    }

    public function count(Query $query): int
    {
        return $query->getQueryType()->getCount($query);
    }
}
