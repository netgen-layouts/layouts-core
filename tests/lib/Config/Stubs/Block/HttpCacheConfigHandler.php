<?php

namespace Netgen\BlockManager\Tests\Config\Stubs\Block;

use Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;

final class HttpCacheConfigHandler implements ConfigDefinitionHandlerInterface
{
    public function getParameterDefinitions()
    {
        return [
            'use_http_cache' => new ParameterDefinition(
                [
                    'name' => 'use_http_cache',
                    'type' => new ParameterType\BooleanType(),
                ]
            ),
            'shared_max_age' => new ParameterDefinition(
                [
                    'name' => 'shared_max_age',
                    'type' => new ParameterType\IntegerType(),
                ]
            ),
        ];
    }

    public function buildParameters(ParameterBuilderInterface $builder)
    {
    }
}
