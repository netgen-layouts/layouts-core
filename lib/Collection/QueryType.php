<?php

namespace Netgen\BlockManager\Collection;

use Netgen\BlockManager\Configuration\QueryType\QueryType as Config;

abstract class QueryType implements QueryTypeInterface
{
    /**
     * @var \Netgen\BlockManager\Configuration\QueryType\QueryType
     */
    protected $config;

    /**
     * Sets the query type configuration.
     *
     * @param \Netgen\BlockManager\Configuration\QueryType\QueryType $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Returns the query type configuration.
     *
     * @return \Netgen\BlockManager\Configuration\QueryType\QueryType
     */
    public function getConfig()
    {
        return $this->config;
    }
}
