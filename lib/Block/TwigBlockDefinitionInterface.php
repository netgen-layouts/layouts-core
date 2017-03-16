<?php

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\API\Values\Block\Block;

interface TwigBlockDefinitionInterface extends BlockDefinitionInterface
{
    /**
     * Returns the name of the Twig block to use.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return string
     */
    public function getTwigBlockName(Block $block);
}
