<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\Utils\HydratorTrait;

/**
 * @final
 */
class ContainerDefinition extends BlockDefinition implements ContainerDefinitionInterface
{
    use HydratorTrait;

    public function getPlaceholders(): array
    {
        return $this->handler->getPlaceholderIdentifiers();
    }
}
