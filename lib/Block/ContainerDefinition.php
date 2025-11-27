<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block;

use Netgen\Layouts\Block\BlockDefinition\ContainerDefinitionHandlerInterface;

final class ContainerDefinition extends AbstractBlockDefinition implements ContainerDefinitionInterface
{
    public private(set) ContainerDefinitionHandlerInterface $handler;

    public array $placeholders {
        get => $this->handler->getPlaceholderIdentifiers();
    }
}
