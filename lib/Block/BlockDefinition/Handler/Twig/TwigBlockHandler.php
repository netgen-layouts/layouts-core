<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler\Twig;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

/**
 * Block used to render a Twig template block with a name provided
 * through the block parameter.
 */
class TwigBlockHandler extends BlockDefinitionHandler implements TwigBlockDefinitionHandlerInterface
{
    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $builder->add(
            'block_name',
            ParameterType\IdentifierType::class
        );
    }

    public function isContextual(Block $block)
    {
        return true;
    }

    public function getTwigBlockName(Block $block)
    {
        return $block->getParameter('block_name')->getValue();
    }
}
