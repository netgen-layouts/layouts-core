<?php

namespace Netgen\BlockManager\Block;

interface ContainerDefinitionInterface extends BlockDefinitionInterface
{
    /**
     * Returns placeholder identifiers.
     *
     * @return string[]
     */
    public function getPlaceholders();

    /**
     * Returns if this block definition is a dynamic container.
     *
     * @return bool
     */
    public function isDynamicContainer();
}
