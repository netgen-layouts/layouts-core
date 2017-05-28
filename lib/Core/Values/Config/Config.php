<?php

namespace Netgen\BlockManager\Core\Values\Config;

use Netgen\BlockManager\API\Values\Config\Config as APIConfig;
use Netgen\BlockManager\Core\Values\ParameterBasedValueTrait;
use Netgen\BlockManager\ValueObject;

class Config extends ValueObject implements APIConfig
{
    use ParameterBasedValueTrait;

    /**
     * @var string
     */
    protected $configKey;

    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionInterface
     */
    protected $definition;

    /**
     * @return string
     */
    public function getConfigKey()
    {
        return $this->configKey;
    }

    /**
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface
     */
    public function getDefinition()
    {
        return $this->definition;
    }
}
