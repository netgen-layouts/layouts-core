<?php

namespace Netgen\BlockManager\Tests\Config\Stubs;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;

class ConfigDefinitionHandler implements ConfigDefinitionHandlerInterface
{
    public function getParameterDefinitions()
    {
        return array(
            'param' => new ParameterDefinition(
                array(
                    'name' => 'param',
                    'type' => new ParameterType\TextLineType(),
                )
            ),
            'param2' => new ParameterDefinition(
                array(
                    'name' => 'param2',
                    'type' => new ParameterType\TextLineType(),
                )
            ),
        );
    }

    public function buildParameters(ParameterBuilderInterface $builder)
    {
    }

    public function isEnabled(ConfigAwareValue $configAwareValue)
    {
        return true;
    }
}
