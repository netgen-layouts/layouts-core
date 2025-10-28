<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\Stubs;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandler as BaseBlockDefinitionHandler;
use Netgen\Layouts\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;

final class TwigBlockDefinitionHandler extends BaseBlockDefinitionHandler implements TwigBlockDefinitionHandlerInterface
{
    /**
     * @param string[] $twigBlocks
     */
    public function __construct(
        private array $twigBlocks = ['twig_block'],
    ) {}

    public function getTwigBlockNames(Block $block): array
    {
        return $this->twigBlocks;
    }

    public function isContextual(Block $block): true
    {
        return true;
    }
}
