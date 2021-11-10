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

    private string $configKey;

    public function getConfigKey(): string
    {
        return $this->configKey;
    }
}
