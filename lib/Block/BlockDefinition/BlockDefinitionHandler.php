<?php

namespace Netgen\BlockManager\Block\BlockDefinition;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Parameters\ParameterDefinition;

abstract class BlockDefinitionHandler implements BlockDefinitionHandlerInterface
{
    const GROUP_CONTENT = 'content';
    const GROUP_DESIGN = 'design';

    /**
     * Returns the array specifying block parameters.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
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
     * @return bool
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
     * @param array $groups
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
     */
    protected function getCommonParameters(array $groups = array())
    {
        return array(
            'css_class' => new ParameterDefinition\TextLine(
                array(),
                false,
                null,
                $groups
            ),
            'css_id' => new ParameterDefinition\TextLine(
                array(),
                false,
                null,
                $groups
            ),
            'set_container' => new ParameterDefinition\Boolean(
                array(),
                false,
                null,
                $groups
            ),
        );
    }
}
