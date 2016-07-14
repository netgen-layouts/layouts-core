<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler as BaseBlockDefinitionHandler;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Parameters\Parameter;

class BlockDefinitionHandler extends BaseBlockDefinitionHandler
{
    /**
     * Returns the array specifying block parameters.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return array(
            'css_class' => new Parameter\TextLine(array()),
            'css_id' => new Parameter\TextLine(array()),
        );
    }

    /**
     * Returns if this block definition should have a collection.
     *
     * @return array
     */
    public function hasCollection()
    {
        return true;
    }

    /**
     * Returns the array of dynamic parameters provided by this block definition.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return array
     */
    public function getDynamicParameters(Block $block)
    {
        return array('definition_param' => 'definition_value');
    }
}
