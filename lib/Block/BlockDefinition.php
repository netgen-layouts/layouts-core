<?php

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Parameters\ParameterCollectionTrait;
use Netgen\BlockManager\ValueObject;

class BlockDefinition extends ValueObject implements BlockDefinitionInterface
{
    use ParameterCollectionTrait;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var \Netgen\BlockManager\Block\PlaceholderDefinitionInterface[]
     */
    protected $placeholders = array();

    /**
     * @var \Netgen\BlockManager\Block\PlaceholderDefinitionInterface
     */
    protected $dynamicPlaceholder;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    protected $handler;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration
     */
    protected $config;

    /**
     * Returns block definition identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns placeholder definitions.
     *
     * @return \Netgen\BlockManager\Block\PlaceholderDefinitionInterface[]
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
     * @return \Netgen\BlockManager\Block\PlaceholderDefinitionInterface
     */
    public function getPlaceholder($placeholderIdentifier)
    {
        if (!$this->hasPlaceholder($placeholderIdentifier)) {
            throw new InvalidArgumentException(
                'placeholderIdentifier',
                sprintf(
                    'Placeholder with "%s" identifier does not exist in block definition.',
                    $placeholderIdentifier
                )
            );
        }

        return $this->placeholders[$placeholderIdentifier];
    }

    /**
     * Returns if block definition has a placeholder definition.
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
     * Returns dynamic placeholder definition.
     *
     * @return \Netgen\BlockManager\Block\PlaceholderDefinitionInterface
     */
    public function getDynamicPlaceholder()
    {
        return $this->dynamicPlaceholder;
    }

    /**
     * Returns the array of dynamic parameters provided by this block definition.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param array $parameters
     *
     * @return array
     */
    public function getDynamicParameters(Block $block, array $parameters = array())
    {
        return $this->handler->getDynamicParameters($block, $parameters);
    }

    /**
     * Returns if this block definition is a container.
     *
     * @return bool
     */
    public function isContainer()
    {
        return $this->handler->isContainer();
    }

    /**
     * Returns if this block definition is a dynamic container.
     *
     * @return bool
     */
    public function isDynamicContainer()
    {
        return $this->handler->isContainer() && $this->handler->isDynamicContainer();
    }

    /**
     * Returns if this block definition should have a collection.
     *
     * @return bool
     */
    public function hasCollection()
    {
        return $this->handler->hasCollection();
    }

    /**
     * Returns the block definition configuration.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }
}
