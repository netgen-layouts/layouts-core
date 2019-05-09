<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\Stubs;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandler as BaseBlockDefinitionHandler;
use Netgen\Layouts\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;

final class TwigBlockDefinitionHandler extends BaseBlockDefinitionHandler implements TwigBlockDefinitionHandlerInterface
{
    public function getTwigBlockName(Block $block): string
    {
        return 'twig_block';
    }

    public function isContextual(Block $block): bool
    {
        return true;
    }
}
