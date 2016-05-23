<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\API\Values\Collection\Query;

class QueryView extends View implements QueryViewInterface
{
    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     */
    public function __construct(Query $query)
    {
        $this->valueObject = $query;
        $this->internalParameters['query'] = $query;
    }

    /**
     * Returns the query.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function getQuery()
    {
        return $this->valueObject;
    }

    /**
     * Returns the view alias.
     *
     * @return string
     */
    public function getAlias()
    {
        return 'query_view';
    }
}
