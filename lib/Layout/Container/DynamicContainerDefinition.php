<?php

namespace Netgen\BlockManager\Layout\Container;

class DynamicContainerDefinition extends ContainerDefinition implements DynamicContainerDefinitionInterface
{
    /**
     * @var \Netgen\BlockManager\Layout\Container\PlaceholderDefinitionInterface
     */
    protected $dynamicPlaceholder;

    /**
     * Returns dynamic placeholder definition.
     *
     * @return \Netgen\BlockManager\Layout\Container\PlaceholderDefinitionInterface
     */
    public function getDynamicPlaceholder()
    {
        return $this->dynamicPlaceholder;
    }
}
