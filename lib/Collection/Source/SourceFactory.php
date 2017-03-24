<?php

namespace Netgen\BlockManager\Collection\Source;

class SourceFactory
{
    /**
     * Builds the source.
     *
     * @param string $identifier
     * @param array $config
     * @param \Netgen\BlockManager\Collection\QueryTypeInterface[] $queryTypes
     *
     * @return \Netgen\BlockManager\Collection\Source\Source
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
