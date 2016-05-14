<?php

namespace Netgen\BlockManager\Configuration\Factory;

use Netgen\BlockManager\Configuration\QueryType\QueryType;

class QueryTypeFactory
{
    /**
     * Builds the query type configuration.
     *
     * @param array $config
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Configuration\QueryType\QueryType
     */
    public static function buildQueryType(array $config, $identifier)
    {
        return new QueryType($identifier, $config['forms']);
    }
}
