<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterDefinition;

class TwigBlockHandler extends BlockDefinitionHandler implements TwigBlockDefinitionHandlerInterface
{
    /**
     * Returns the array specifying block parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
     */
    public function getParameters()
    {
        return array(
            'block_name' => new ParameterDefinition\Identifier(),
        ) + $this->getCommonParameters();
    }

    /**
     * Returns the name of the Twig block to use.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return string
     */
    public function getTwigBlockName(Block $block)
    {
        return $block->getParameter('block_name')->getValue();
    }
}
