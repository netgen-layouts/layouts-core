<?php

namespace Netgen\BlockManager\BlockDefinition;

use Netgen\BlockManager\Parameters\Parameter;

abstract class BlockDefinition implements BlockDefinitionInterface
{
    /**
     * Returns the array specifying block parameters.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\Parameters\Parameter[]
     */
    public function getParameters()
    {
        return array(
            'css_id' => new Parameter\Text('CSS ID'),
            'css_class' => new Parameter\Text('CSS class'),
        );
    }

    /**
     * Returns the array specifying block parameter validator constraints.
     *
     * @return array
     */
    public function getParameterConstraints()
    {
        return array();
    }
}
