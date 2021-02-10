<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Result;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\Item\VisibilityResolverInterface;
use Netgen\Layouts\Item\CmsItemBuilderInterface;

final class CollectionRunnerFactory
{
    private CmsItemBuilderInterface $cmsItemBuilder;

    private VisibilityResolverInterface $visibilityResolver;

    public function __construct(CmsItemBuilderInterface $cmsItemBuilder, VisibilityResolverInterface $visibilityResolver)
    {
        $this->cmsItemBuilder = $cmsItemBuilder;
        $this->visibilityResolver = $visibilityResolver;
    }

    /**
     * Builds and returns the collection runner for provided collection.
     */
    public function getCollectionRunner(Collection $collection, int $flags = 0): CollectionRunnerInterface
    {
        $collectionQuery = $collection->getQuery();

        if ($collectionQuery instanceof Query) {
            $queryRunner = $this->getQueryRunner($collectionQuery, $flags);

            return new DynamicCollectionRunner($queryRunner, $this->visibilityResolver);
        }

        return new ManualCollectionRunner($this->visibilityResolver);
    }

    /**
     * Builds the query runner for the provided query based on provided flags.
     */
    private function getQueryRunner(Query $query, int $flags = 0): QueryRunnerInterface
    {
        $showUnknownItems = (bool) ($flags & ResultSet::INCLUDE_UNKNOWN_ITEMS);

        if ($showUnknownItems && $query->isContextual()) {
            return new ContextualQueryRunner();
        }

        return new QueryRunner($this->cmsItemBuilder);
    }
}
