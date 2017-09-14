<?php

namespace Netgen\BlockManager\Block;

class ContainerDefinition extends BlockDefinition implements ContainerDefinitionInterface
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface
     */
    protected $handler;

    public function getPlaceholders()
    {
        if ($this->isDynamicContainer()) {
            return array();
        }

        return $this->handler->getPlaceholderIdentifiers();
    }

    public function isDynamicContainer()
    {
        return $this->handler->isDynamicContainer();
    }
}
