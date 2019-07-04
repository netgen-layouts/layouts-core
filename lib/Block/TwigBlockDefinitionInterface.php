<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block;

use Netgen\Layouts\API\Values\Block\Block;

/**
 * Twig block definition is a special kind of block used to render
 * template blocks specified in the currently rendered context. This
 * model specifies, for every block definition, the name of the Twig blocks
 * to render. First block that is found is rendered.
 */
interface TwigBlockDefinitionInterface extends BlockDefinitionInterface
{
    /**
     * Returns the names of the Twig blocks to render.
     *
     * First block that is found is rendered.
     *
     * @return string[]
     */
    public function getTwigBlockNames(Block $block): array;
}
