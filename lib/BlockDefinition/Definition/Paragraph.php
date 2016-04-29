<?php

namespace Netgen\BlockManager\BlockDefinition\Definition;

use Netgen\BlockManager\BlockDefinition\BlockDefinition;
use Netgen\BlockManager\BlockDefinition\Parameter;
use Netgen\BlockManager\API\Values\Page\Block;
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
     * @return \Netgen\BlockManager\BlockDefinition\Parameter\Parameter[]
     */
    public function getParameters()
    {
        return array(
            'content' => new Parameter\Text('Content', true),
        ) + parent::getParameters();
    }

    /**
     * Returns the array specifying block parameter validator constraints.
     *
     * @return array
     */
    public function getParameterConstraints()
    {
        return array(
            'content' => array(
                new Constraints\NotBlank(),
            ),
        ) + parent::getParameterConstraints();
    }

    /**
     * Returns the array of values provided by this block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param array $parameters
     *
     * @return array
     */
    public function getValues(Block $block, array $parameters = array())
    {
        return array();
    }
}
