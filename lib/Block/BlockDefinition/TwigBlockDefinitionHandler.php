<?php

namespace Netgen\BlockManager\Block\BlockDefinition;

use Netgen\BlockManager\API\Values\Page\Block;

abstract class TwigBlockDefinitionHandler extends BlockDefinitionHandler implements TwigBlockDefinitionHandlerInterface
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
        return $block->getParameter($this->getTwigBlockParameter());
    }

    /**
     * Returns the name of the parameter which will provide the Twig block name.
     *
     * @return string
     */
    abstract public function getTwigBlockParameter();
}
