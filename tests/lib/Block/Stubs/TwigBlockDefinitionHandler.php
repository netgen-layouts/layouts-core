<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;

final class TwigBlockDefinitionHandler extends BlockDefinitionHandler implements TwigBlockDefinitionHandlerInterface
{
    public function getTwigBlockName(Block $block)
    {
        return 'twig_block';
    }

    public function isContextual(Block $block)
    {
        return true;
    }
}
