<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\API\Values\Collection\Query;

class QueryView extends View implements QueryViewInterface
{
    /**
     * Returns the query.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function getQuery()
    {
        return $this->value;
    }

    /**
     * Sets the query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     */
    public function setQuery(Query $query)
    {
        $this->value = $query;
        $this->internalParameters['query'] = $this->value;
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
