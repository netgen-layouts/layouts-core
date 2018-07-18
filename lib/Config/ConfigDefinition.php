<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Config;

use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionTrait;
use Netgen\BlockManager\Utils\HydratorTrait;

/**
 * @final
 */
class ConfigDefinition implements ConfigDefinitionInterface
{
    use HydratorTrait;
    use ParameterDefinitionCollectionTrait;

    /**
     * @var string
     */
    private $configKey;

    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface
     */
    private $handler;

    public function getConfigKey(): string
    {
        return $this->configKey;
    }
}
