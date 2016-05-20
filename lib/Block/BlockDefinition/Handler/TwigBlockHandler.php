<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Parameters\Parameter;

class TwigBlockHandler extends BlockDefinitionHandler implements BlockDefinitionHandlerInterface
{
    const DEFINITION_IDENTIFIER = 'twig_block';

    /**
     * Returns the array specifying block parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return array(
            'block_name' => new Parameter\Identifier(array(), true),
        );
    }
}
