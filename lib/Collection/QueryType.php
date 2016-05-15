<?php

namespace Netgen\BlockManager\Collection;

use Netgen\BlockManager\Configuration\QueryType\QueryType as Configuration;

abstract class QueryType implements QueryTypeInterface
{
    /**
     * @var \Netgen\BlockManager\Configuration\QueryType\QueryType
     */
    protected $configuration;

    /**
     * Sets the query type configuration.
     *
     * @param \Netgen\BlockManager\Configuration\QueryType\QueryType $configuration
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Returns the query type configuration.
     *
     * @return \Netgen\BlockManager\Configuration\QueryType\QueryType $configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
