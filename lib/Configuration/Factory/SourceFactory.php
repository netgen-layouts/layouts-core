<?php

namespace Netgen\BlockManager\Configuration\Factory;

use Netgen\BlockManager\Configuration\Source\Source;
use Netgen\BlockManager\Configuration\Source\Query;

class SourceFactory
{
    /**
     * Builds the source.
     *
     * @param string $identifier
     * @param array $config
     * @param \Netgen\BlockManager\Collection\QueryTypeInterface[] $queryTypes
     *
     * @return \Netgen\BlockManager\Configuration\Source\Source
     */
    public static function buildSource($identifier, array $config, array $queryTypes)
    {
        $queries = array();

        foreach ($config['queries'] as $queryIdentifier => $queryConfig) {
            $queries[$queryIdentifier] = new Query(
                array(
                    'identifier' => $queryIdentifier,
                    'queryType' => $queryTypes[$queryIdentifier],
                    'defaultParameters' => $queryConfig['default_parameters'],
                )
            );
        }

        return new Source(
            array(
                'identifier' => $identifier,
                'name' => $config['name'],
                'queries' => $queries,
            )
        );
    }
}
