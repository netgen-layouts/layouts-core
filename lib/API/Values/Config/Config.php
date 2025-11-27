<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Config;

use Netgen\Layouts\Config\ConfigDefinitionInterface;
use Netgen\Layouts\Parameters\ParameterCollectionInterface;
use Netgen\Layouts\Parameters\ParameterCollectionTrait;
use Netgen\Layouts\Utils\HydratorTrait;

final class Config implements ParameterCollectionInterface
{
    use HydratorTrait;
    use ParameterCollectionTrait;

    /**
     * Returns the config key.
     */
    public private(set) string $configKey;

    /**
     * Returns the config definition.
     */
    public private(set) ConfigDefinitionInterface $definition;
}
