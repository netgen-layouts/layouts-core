<?php

namespace Netgen\BlockManager\Block;

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
