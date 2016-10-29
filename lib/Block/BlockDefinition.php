<?php

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;

class BlockDefinition implements BlockDefinitionInterface
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    protected $handler;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration
     */
    protected $config;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
     */
    protected $parameters;

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface $handler
     * @param \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration $config
     */
    public function __construct($identifier, BlockDefinitionHandlerInterface $handler, Configuration $config)
    {
        $this->identifier = $identifier;
        $this->handler = $handler;
        $this->config = $config;
    }

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
     * Returns the array specifying block parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
     */
    public function getParameters()
    {
        if ($this->parameters === null) {
            $this->parameters = $this->handler->getParameters();
        }

        return $this->parameters;
    }

    /**
     * Returns the array of dynamic parameters provided by this block definition.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Netgen\BlockManager\Block\DynamicParameters
     */
    public function getDynamicParameters(Block $block)
    {
        return new DynamicParameters($this->handler->getDynamicParameters($block));
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
     * @return \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    public function getHandler()
    {
        return $this->handler;
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
