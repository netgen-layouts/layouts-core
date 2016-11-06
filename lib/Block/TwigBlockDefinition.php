<?php

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\API\Values\Page\Block;

class TwigBlockDefinition extends BlockDefinition implements TwigBlockDefinitionInterface
{
    /**
     * Returns the name of the Twig block to use.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return string
     */
    public function getTwigBlockName(Block $block)
    {
        return $this->handler->getTwigBlockName($block);
    }
}
