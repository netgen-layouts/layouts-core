<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block;

use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandlerInterface;

/**
 * @final
 */
class ContainerDefinition extends AbstractBlockDefinition implements ContainerDefinitionInterface
{
    /**
     * @var \Netgen\Layouts\Block\BlockDefinition\ContainerDefinitionHandlerInterface
     */
    private $handler;

    public function getPlaceholders(): array
    {
        return $this->handler->getPlaceholderIdentifiers();
    }

    protected function getHandler(): BlockDefinitionHandlerInterface
    {
        return $this->handler;
    }
}
