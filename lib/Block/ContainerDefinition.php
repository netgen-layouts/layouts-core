<?php

namespace Netgen\BlockManager\Block;

class ContainerDefinition extends BlockDefinition implements ContainerDefinitionInterface
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface
     */
    protected $handler;

    /**
     * Returns placeholder identifiers.
     *
     * @return string[]
     */
    public function getPlaceholders()
    {
        if ($this->isDynamicContainer()) {
            return array();
        }

        return $this->handler->getPlaceholderIdentifiers();
    }

    /**
     * Returns if this block definition is a dynamic container.
     *
     * @return bool
     */
    public function isDynamicContainer()
    {
        return $this->handler->isDynamicContainer();
    }
}
