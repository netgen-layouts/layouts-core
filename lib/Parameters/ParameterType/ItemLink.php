<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Validator\Constraint\ItemLink as ItemLinkConstraint;

class ItemLink extends ParameterType
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'item_link';
    }

    /**
     * Returns constraints that will be used to validate the parameter value.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinition $parameterDefinition
     * @param mixed $value
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getValueConstraints(ParameterDefinition $parameterDefinition, $value)
    {
        return array(
            new ItemLinkConstraint(),
        );
    }
}
