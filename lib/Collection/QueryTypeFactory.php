<?php

namespace Netgen\BlockManager\Collection;

use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

class QueryTypeFactory
{
    /**
     * Builds the query type.
     *
     * @param string $type
     * @param \Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface $handler
     * @param \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration $config
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $parameterBuilder
     *
     * @return \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    public static function buildQueryType(
        $type,
        QueryTypeHandlerInterface $handler,
        Configuration $config,
        ParameterBuilderInterface $parameterBuilder
    ) {
        return new QueryType(
            array(
                'type' => $type,
                'handler' => $handler,
                'config' => $config,
                'parameterBuilder' => $parameterBuilder,
            )
        );
    }
}
