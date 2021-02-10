<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block;

use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandlerInterface;

/**
 * @final
 */
class BlockDefinition extends AbstractBlockDefinition
{
    private BlockDefinitionHandlerInterface $handler;

    protected function getHandler(): BlockDefinitionHandlerInterface
    {
        return $this->handler;
    }
}
