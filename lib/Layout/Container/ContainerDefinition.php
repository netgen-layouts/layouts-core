<?php

namespace Netgen\BlockManager\Layout\Container;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Parameters\ParameterCollectionTrait;
use Netgen\BlockManager\ValueObject;

class ContainerDefinition extends ValueObject implements ContainerDefinitionInterface
{
    use ParameterCollectionTrait;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var \Netgen\BlockManager\Layout\Container\PlaceholderDefinitionInterface[]
     */
    protected $placeholders = array();

    /**
     * @var \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration
     */
    protected $config;

    /**
     * Returns container definition identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns container placeholder definitions.
     *
     * @return \Netgen\BlockManager\Layout\Container\PlaceholderDefinitionInterface[]
     */
    public function getPlaceholders()
    {
        return $this->placeholders;
    }

    /**
     * Returns a placeholder definition.
     *
     * @param string $placeholderIdentifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException if the placeholder does not exist
     *
     * @return \Netgen\BlockManager\Layout\Container\PlaceholderDefinitionInterface
     */
    public function getPlaceholder($placeholderIdentifier)
    {
        if (!$this->hasPlaceholder($placeholderIdentifier)) {
            throw new InvalidArgumentException(
                'placeholderIdentifier',
                sprintf(
                    'Placeholder with "%s" identifier does not exist in container definition.',
                    $placeholderIdentifier
                )
            );
        }

        return $this->placeholders[$placeholderIdentifier];
    }

    /**
     * Returns if container definition has a placeholder definition.
     *
     * @param string $placeholderIdentifier
     *
     * @return bool
     */
    public function hasPlaceholder($placeholderIdentifier)
    {
        return isset($this->placeholders[$placeholderIdentifier]);
    }

    /**
     * Returns the container definition configuration.
     *
     * @return \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }
}
