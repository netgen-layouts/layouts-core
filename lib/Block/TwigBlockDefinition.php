<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block;

use Netgen\Layouts\API\Values\Block\Block;

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
