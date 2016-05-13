<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\API\Values\Collection\Query;

interface QueryViewInterface extends ViewInterface
{
    /**
     * Returns the query.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function getQuery();

    /**
     * Sets the query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     */
    public function setQuery(Query $query);
}
