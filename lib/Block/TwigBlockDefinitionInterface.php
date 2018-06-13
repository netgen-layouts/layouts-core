<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\API\Values\Block\Block;

/**
 * Twig block definition is a special kind of block used to render
 * template blocks specified in the currently rendered context. This
 * model specifies, for every block definition, the name of the Twig block
 * to render.
 */
interface TwigBlockDefinitionInterface extends BlockDefinitionInterface
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
