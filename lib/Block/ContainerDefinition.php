<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block;

/**
 * @final
 */
class ContainerDefinition extends BlockDefinition implements ContainerDefinitionInterface
{
    public function getPlaceholders(): array
    {
        return $this->handler->getPlaceholderIdentifiers();
    }
}
