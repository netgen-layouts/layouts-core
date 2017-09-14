<?php

namespace Netgen\BlockManager\Collection;

use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface;

class QueryTypeFactory
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface
     */
    protected $parameterBuilderFactory;

    public function __construct(ParameterBuilderFactoryInterface $parameterBuilderFactory)
    {
        $this->parameterBuilderFactory = $parameterBuilderFactory;
    }

    /**
     * Builds the query type.
     *
     * @param string $type
     * @param \Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface $handler
     * @param \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration $config
     *
     * @return \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    public function buildQueryType(
        $type,
        QueryTypeHandlerInterface $handler,
        Configuration $config
    ) {
        $parameterBuilder = $this->parameterBuilderFactory->createParameterBuilder();
        $handler->buildParameters($parameterBuilder);
        $parameters = $parameterBuilder->buildParameters();

        return new QueryType(
            array(
                'type' => $type,
                'handler' => $handler,
                'config' => $config,
                'parameters' => $parameters,
            )
        );
    }
}
