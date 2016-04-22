<?php

namespace Netgen\BlockManager\BlockDefinition;

abstract class BlockDefinition implements BlockDefinitionInterface
{
    /**
     * Returns the array specifying block parameters.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\BlockDefinition\Parameter\Parameter[]
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
