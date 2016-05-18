<?php

namespace Netgen\BlockManager\View;

interface QueryViewInterface extends ViewInterface
{
    /**
     * Returns the query.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Query
     */
    public function getQuery();
}
