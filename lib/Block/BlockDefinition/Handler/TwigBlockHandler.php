<?php

namespace Netgen\BlockManager\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandler;
use Netgen\BlockManager\Parameters\Parameter;

class TwigBlockHandler extends TwigBlockDefinitionHandler
{
    /**
     * Returns the array specifying block parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return array(
            'block_name' => new Parameter\Identifier(),
        );
    }

    /**
     * Returns the name of the parameter which will provide the Twig block name.
     *
     * @return string
     */
    public function getTwigBlockParameter()
    {
        return 'block_name';
    }
}
