<?php

declare(strict_types=1);

namespace Netgen\Layouts\Config;

use Netgen\Layouts\Parameters\ParameterDefinitionCollectionTrait;
use Netgen\Layouts\Utils\HydratorTrait;

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
     * @var \Netgen\Layouts\Config\ConfigDefinitionHandlerInterface
     */
    private $handler;

    public function getConfigKey(): string
    {
        return $this->configKey;
    }
}
