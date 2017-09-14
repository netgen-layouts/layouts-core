<?php

namespace Netgen\BlockManager\Block\BlockDefinition;

/**
 * Container definition handler represents the dynamic/runtime part of the
 * Container definition.
 *
 * Implement this interface to create your own custom container blocks.
 */
interface ContainerDefinitionHandlerInterface extends BlockDefinitionHandlerInterface
{
    /**
     * Returns if this container definition is a dynamic container.
     *
     * @return bool
     */
    public function isDynamicContainer();

    /**
     * Returns all placeholder identifiers for this container definition.
     *
     * @return array
     */
    public function getPlaceholderIdentifiers();
}
