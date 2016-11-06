<?php

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\API\Values\Page\Block;
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
     * Returns the block definition configuration.
     *
     * @return \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }
}
