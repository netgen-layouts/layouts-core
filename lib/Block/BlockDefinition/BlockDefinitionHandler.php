<?php

namespace Netgen\BlockManager\Block\BlockDefinition;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Parameters\Parameter;

abstract class BlockDefinitionHandler implements BlockDefinitionHandlerInterface
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
        return array();
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
        return array();
    }

    /**
     * Returns if this block definition should have a collection.
     *
     * @return array
     */
    public function hasCollection()
    {
        return false;
    }

    /**
     * Returns the array specifying the parameters most block will use.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    protected function getCommonParameters()
    {
        return array(
            'css_class' => new Parameter\TextLine(),
            'css_id' => new Parameter\TextLine(),
        );
    }
}
