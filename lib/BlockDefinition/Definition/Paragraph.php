<?php

namespace Netgen\BlockManager\BlockDefinition;

class Paragraph extends BlockDefinition implements BlockDefinitionInterface
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
     * @return \Netgen\BlockManager\BlockDefinition\Parameter[]
     */
    public function getParameters()
    {
        return  array(
            new Parameters\Text(
                'content',
                'Content',
                null,
                'Text'
            ),
        ) + parent::getParameters();
    }

    /**
     * Returns the array of values provided by this block.
     *
     * @param array $parameters
     *
     * @return array
     */
    public function getValues(array $parameters = array())
    {
        return array();
    }
}
