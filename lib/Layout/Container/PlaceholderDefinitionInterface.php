<?php

namespace Netgen\BlockManager\Layout\Container;

use Netgen\BlockManager\Parameters\ParameterCollectionInterface;

interface PlaceholderDefinitionInterface extends ParameterCollectionInterface
{
    /**
     * Returns placeholder identifier.
     *
     * @return string
     */
    public function getIdentifier();
}
