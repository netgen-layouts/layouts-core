<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\API\Values\Block\Block;

/**
 * @final
 */
class TwigBlockDefinition extends BlockDefinition implements TwigBlockDefinitionInterface
{
    public function getTwigBlockName(Block $block): string
    {
        return $this->handler->getTwigBlockName($block);
    }
}
