<?php

namespace Netgen\BlockManager\Config;

use Netgen\BlockManager\Parameters\ParameterCollectionInterface;

interface ConfigDefinitionInterface extends ParameterCollectionInterface
{
    /**
     * Returns the type of the config definition.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns config definition identifier.
     *
     * @return string
     */
    public function getIdentifier();
}
