<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\API\Values\Collection\Query;

class QueryView extends View implements QueryViewInterface
{
    /**
     * @var \Netgen\BlockManager\API\Values\Collection\Query
     */
    protected $query;

    /**
     * Returns the query.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Sets the query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     */
    public function setQuery(Query $query)
    {
        $this->query = $query;
        $this->internalParameters['query'] = $this->query;
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
