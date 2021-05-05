<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\QueryType;

use Netgen\Layouts\Parameters\ParameterBuilderFactoryInterface;

final class QueryTypeFactory
{
    private ParameterBuilderFactoryInterface $parameterBuilderFactory;

    public function __construct(ParameterBuilderFactoryInterface $parameterBuilderFactory)
    {
        $this->parameterBuilderFactory = $parameterBuilderFactory;
    }

    /**
     * Builds the query type.
     *
     * @param array<string, mixed> $config
     */
    public function buildQueryType(string $type, QueryTypeHandlerInterface $handler, array $config): QueryTypeInterface
    {
        $parameterBuilder = $this->parameterBuilderFactory->createParameterBuilder();
        $handler->buildParameters($parameterBuilder);
        $parameterDefinitions = $parameterBuilder->buildParameterDefinitions();

        return QueryType::fromArray(
            [
                'type' => $type,
                'isEnabled' => $config['enabled'],
                'name' => $config['name'] ?? '',
                'handler' => $handler,
                'parameterDefinitions' => $parameterDefinitions,
            ],
        );
    }
}
