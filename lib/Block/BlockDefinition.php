<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block;

use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandlerInterface;

/**
 * @final
 */
class BlockDefinition extends AbstractBlockDefinition
{
    /**
     * @var \Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    private $handler;

    protected function getHandler(): BlockDefinitionHandlerInterface
    {
        return $this->handler;
    }
}
