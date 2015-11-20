<?php

namespace Netgen\BlockManager\BlockDefinition\Definition;

use Netgen\BlockManager\BlockDefinition\BlockDefinition;
use Netgen\BlockManager\BlockDefinition\Parameters;
use Netgen\BlockManager\API\Values\Page\Block;

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
     * @return \Netgen\BlockManager\BlockDefinition\Parameter[]
     */
    public function getParameters()
    {
        return array(
            'content' => new Parameters\Text('Text'),
        ) + parent::getParameters();
    }

    /**
     * Returns the array specifying block parameter human readable names.
     *
     * @return string[]
     */
    public function getParameterNames()
    {
        return array(
            'content' => 'Content',
        ) + parent::getParameterNames();
    }

    /**
     * Returns the array of values provided by this block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return array
     */
    public function getValues(Block $block)
    {
        return array();
    }
}
