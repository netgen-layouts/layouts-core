<?php

declare(strict_types=1);

namespace Netgen\Layouts\Config;

use Netgen\Layouts\Parameters\ParameterBuilderInterface;

/**
 * Config definition handler represents the dynamic/runtime part of the
 * config definitions.
 *
 * Implement this interface to create your own config definitions for an entity.
 */
interface ConfigDefinitionHandlerInterface
{
    /**
     * Builds the parameters by using provided parameter builder.
     */
    public function buildParameters(ParameterBuilderInterface $builder): void;
}
