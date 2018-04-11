<?php

namespace Netgen\BlockManager\Tests\Config\Stubs;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

final class DisabledConfigDefinitionHandler implements ConfigDefinitionHandlerInterface
{
    public function getParameterDefinitions()
    {
        return array();
    }

    public function buildParameters(ParameterBuilderInterface $builder)
    {
    }

    public function isEnabled(ConfigAwareValue $configAwareValue)
    {
        return false;
    }
}
