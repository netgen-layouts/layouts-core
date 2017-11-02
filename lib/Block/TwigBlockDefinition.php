<?php

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\API\Values\Block\Block;

/**
 * @final
 */
class TwigBlockDefinition extends BlockDefinition implements TwigBlockDefinitionInterface
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface
     */
    protected $handler;

    public function getTwigBlockName(Block $block)
    {
        return $this->handler->getTwigBlockName($block);
    }
}
