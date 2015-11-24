<?php

namespace Netgen\BlockManager\BlockDefinition;

abstract class BlockDefinition implements BlockDefinitionInterface
{
    /**
     * Returns the array specifying block parameters.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\BlockDefinition\Parameter[]
     */
    public function getParameters()
    {
        return array(
            'css_id' => new Parameter\Text(),
            'css_class' => new Parameter\Text(),
        );
    }

    /**
     * Returns the array specifying block parameter human readable names.
     *
     * @return string[]
     */
    public function getParameterNames()
    {
        return array(
            'css_id' => 'CSS ID',
            'css_class' => 'CSS class',
        );
    }

    /**
     * Returns the array specifying block parameter validator constraints.
     *
     * @return array
     */
    public function getParameterConstraints()
    {
        return array(
            'css_id' => false,
            'css_class' => false,
        );
    }
}
