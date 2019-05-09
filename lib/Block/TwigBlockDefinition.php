<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandlerInterface;

/**
 * @final
 */
class TwigBlockDefinition extends AbstractBlockDefinition implements TwigBlockDefinitionInterface
{
    /**
     * @var \Netgen\Layouts\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface
     */
    private $handler;

    public function getTwigBlockName(Block $block): string
    {
        return $this->handler->getTwigBlockName($block);
    }

    protected function getHandler(): BlockDefinitionHandlerInterface
    {
        return $this->handler;
    }
}
