<?php

namespace Netgen\BlockManager\Block\BlockDefinition;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Parameters\Parameter;

class TwigBlock extends BlockDefinition
{
    const DEFINITION_IDENTIFIER = 'twig_block';

    /**
     * Returns block definition identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return self::DEFINITION_IDENTIFIER;
    }

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
