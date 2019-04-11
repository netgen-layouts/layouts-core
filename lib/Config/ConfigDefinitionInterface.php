<?php

declare(strict_types=1);

namespace Netgen\Layouts\Config;

use Netgen\Layouts\Parameters\ParameterDefinitionCollectionInterface;

/**
 * Config definition represents an abstract concept reusable by all
 * entities which allows specification and validation of entity configuration
 * stored in the database. For example, blocks could use these definitions
 * to specify how the block HTTP cache config is stored and validated.
 */
interface ConfigDefinitionInterface extends ParameterDefinitionCollectionInterface
{
    /**
     * Returns the config key for the definition.
     */
    public function getConfigKey(): string;
}
