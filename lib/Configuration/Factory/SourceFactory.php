<?php

namespace Netgen\BlockManager\Configuration\Factory;

use Netgen\BlockManager\Configuration\Source\Source;
use Netgen\BlockManager\Configuration\Source\Query;

class SourceFactory
{
    /**
     * Builds the source.
     *
     * @param array $config
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Configuration\Source\Source
     */
    public static function buildSource(array $config, $identifier)
    {
        $queries = array();

        foreach ($config['queries'] as $queryIdentifier => $queryConfig) {
            $queries[$queryIdentifier] = new Query(
                $queryIdentifier,
                $queryConfig['query_type'],
                $queryConfig['default_parameters']
            );
        }

        return new Source($identifier, $config['enabled'], $config['name'], $queries);
    }
}
