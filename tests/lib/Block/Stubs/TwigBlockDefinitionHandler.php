<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\Stubs;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandler as BaseBlockDefinitionHandler;
use Netgen\Layouts\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;

final class TwigBlockDefinitionHandler extends BaseBlockDefinitionHandler implements TwigBlockDefinitionHandlerInterface
{
    /**
     * @var string[]
     */
    private array $twigBlocks;

    /**
     * @param string[] $twigBlocks
     */
    public function __construct(array $twigBlocks = ['twig_block'])
    {
        $this->twigBlocks = $twigBlocks;
    }

    public function getTwigBlockNames(Block $block): array
    {
        return $this->twigBlocks;
    }

    public function isContextual(Block $block): bool
    {
        return true;
    }
}
