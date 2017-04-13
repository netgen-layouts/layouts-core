<?php

namespace Netgen\BlockManager\Block\BlockDefinition;

interface ContainerDefinitionHandlerInterface extends BlockDefinitionHandlerInterface
{
    /**
     * Returns if this block definition is a dynamic container.
     *
     * @return bool
     */
    public function isDynamicContainer();

    /**
     * Returns placeholder identifiers.
     *
     * @return array
     */
    public function getPlaceholderIdentifiers();
}
