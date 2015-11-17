<?php

namespace Netgen\BlockManager\BlockDefinition\Definition;

use Netgen\BlockManager\BlockDefinition\BlockDefinition;
use Netgen\BlockManager\BlockDefinition\Parameters;

class Title extends BlockDefinition
{
    /**
     * Returns block definition identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'title';
    }

    /**
     * Returns the array specifying block parameters.
     *
     * @return \Netgen\BlockManager\BlockDefinition\Parameter[]
     */
    public function getParameters()
    {
        return array_merge(
            array(
                new Parameters\Select(
                    'tag',
                    'Tag',
                    array(
                        'options' => array(
                            'h1' => 'h1',
                            'h2' => 'h2',
                            'h3' => 'h3',
                        ),
                    ),
                    'h2'
                ),
                new Parameters\Text('title', 'Title', array(), 'Title'),
            ),
            parent::getParameters()
        );
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
