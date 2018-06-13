<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block;

/**
 * @final
 */
class ContainerDefinition extends BlockDefinition implements ContainerDefinitionInterface
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface
     */
    protected $handler;

    public function getPlaceholders()
    {
        return $this->handler->getPlaceholderIdentifiers();
    }
}
