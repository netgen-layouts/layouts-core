<?php

namespace Netgen\BlockManager\Core\Values\Config;

use Netgen\BlockManager\API\Values\Config\Config as APIConfig;
use Netgen\BlockManager\Core\Values\ParameterAwareValueTrait;
use Netgen\BlockManager\ValueObject;

final class Config extends ValueObject implements APIConfig
{
    use ParameterAwareValueTrait;

    /**
     * @var string
     */
    protected $configKey;

    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionInterface
     */
    protected $definition;

    public function getConfigKey()
    {
        return $this->configKey;
    }

    public function getDefinition()
    {
        return $this->definition;
    }
}
