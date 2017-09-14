<?php

namespace Netgen\BlockManager\Block\BlockDefinition;

use Netgen\BlockManager\API\Values\Block\Block;

/**
 * Twig block handler represents the dynamic/runtime part of the
 * Twig block definition.
 *
 * Implement this interface to create your own custom Twig blocks.
 */
interface TwigBlockDefinitionHandlerInterface extends BlockDefinitionHandlerInterface
{
    /**
     * Returns the name of the Twig block to render.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return string
     */
    public function getTwigBlockName(Block $block);
}
