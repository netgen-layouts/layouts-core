<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockDefinition;

use Netgen\Layouts\API\Values\Block\Block;

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
     */
    public function getTwigBlockName(Block $block): string;
}
