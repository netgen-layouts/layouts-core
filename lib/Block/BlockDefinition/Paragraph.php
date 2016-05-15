<?php

namespace Netgen\BlockManager\Block\BlockDefinition;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Parameters\Parameter;
use Symfony\Component\Validator\Constraints;

class Paragraph extends BlockDefinition
{
    /**
     * Returns block definition identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'paragraph';
    }

    /**
     * Returns the array specifying block parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return array(
            'content' => new Parameter\Text(array(), true),
        ) + parent::getParameters();
    }
}
