<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Utils\HydratorTrait;

/**
 * @final
 */
class TwigBlockDefinition extends BlockDefinition implements TwigBlockDefinitionInterface
{
    use HydratorTrait;

    public function getTwigBlockName(Block $block): string
    {
        return $this->handler->getTwigBlockName($block);
    }
}
