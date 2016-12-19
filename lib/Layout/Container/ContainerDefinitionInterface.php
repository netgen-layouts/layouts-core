<?php

namespace Netgen\BlockManager\Layout\Container;

use Netgen\BlockManager\Parameters\ParameterCollectionInterface;

interface ContainerDefinitionInterface extends ParameterCollectionInterface
{
    /**
     * Returns container definition identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns container placeholder definitions.
     *
     * @return \Netgen\BlockManager\Layout\Container\PlaceholderDefinitionInterface[]
     */
    public function getPlaceholders();

    /**
     * Returns a placeholder definition.
     *
     * @param string $placeholderIdentifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException if the placeholder does not exist
     *
     * @return \Netgen\BlockManager\Layout\Container\PlaceholderDefinitionInterface
     */
    public function getPlaceholder($placeholderIdentifier);

    /**
     * Returns if container definition has a placeholder definition.
     *
     * @param string $placeholderIdentifier
     *
     * @return bool
     */
    public function hasPlaceholder($placeholderIdentifier);

    /**
     * Returns the container definition configuration.
     *
     * @return \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration
     */
    public function getConfig();
}
