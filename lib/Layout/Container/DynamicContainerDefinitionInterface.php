<?php

namespace Netgen\BlockManager\Layout\Container;

interface DynamicContainerDefinitionInterface extends ContainerDefinitionInterface
{
    /**
     * Returns dynamic placeholder definition.
     *
     * @return \Netgen\BlockManager\Layout\Container\PlaceholderDefinitionInterface
     */
    public function getDynamicPlaceholder();
}
