<?php

namespace Netgen\BlockManager\Collection;

interface QueryTypeInterface
{
    /**
     * Returns the query type.
     *
     * @return string
     */
    public function getType();

    /**
     * @return \Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface
     */
    public function getHandler();

    /**
     * Returns the query type configuration.
     *
     * @return \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration
     */
    public function getConfig();
}
