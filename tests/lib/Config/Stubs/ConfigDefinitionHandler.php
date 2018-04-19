<?php

namespace Netgen\BlockManager\Tests\Config\Stubs;

use Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;

final class ConfigDefinitionHandler implements ConfigDefinitionHandlerInterface
{
    public function getParameterDefinitions()
    {
        return [
            'param' => new ParameterDefinition(
                [
                    'name' => 'param',
                    'type' => new ParameterType\TextLineType(),
                ]
            ),
            'param2' => new ParameterDefinition(
                [
                    'name' => 'param2',
                    'type' => new ParameterType\TextLineType(),
                ]
            ),
        ];
    }

    public function buildParameters(ParameterBuilderInterface $builder)
    {
    }
}
