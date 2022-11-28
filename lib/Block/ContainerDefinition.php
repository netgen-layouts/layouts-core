<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block;

use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\Layouts\Block\BlockDefinition\ContainerDefinitionHandlerInterface;

/**
 * @final
 */
class ContainerDefinition extends AbstractBlockDefinition implements ContainerDefinitionInterface
{
    private ContainerDefinitionHandlerInterface $handler;

    public function getPlaceholders(): array
    {
        return $this->handler->getPlaceholderIdentifiers();
    }

    public function getHandler(): BlockDefinitionHandlerInterface
    {
        return $this->handler;
    }
}
