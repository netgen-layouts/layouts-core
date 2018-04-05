<?php

namespace Netgen\BlockManager\Tests\Config\Stubs;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Config\ConfigDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionTrait;

final class ConfigDefinitionWithParameterDefinitions implements ConfigDefinitionInterface
{
    use ParameterCollectionTrait;

    public function __construct(array $parameterDefinitions)
    {
        $this->parameterDefinitions = $parameterDefinitions;
    }

    public function getConfigKey()
    {
    }

    public function isEnabled(ConfigAwareValue $configAwareValue)
    {
    }
}
