<?php

namespace Netgen\BlockManager\Parameters\Parameter\Compound;

use Netgen\BlockManager\Parameters\CompoundParameter;
use Symfony\Component\Validator\Constraints;

class Boolean extends CompoundParameter
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'compound_boolean';
    }

    /**
     * Returns constraints that are specific to parameter.
     *
     * @param array $groups
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    protected function getParameterConstraints(array $groups = null)
    {
        return array(
            new Constraints\Type(
                array(
                    'type' => 'bool',
                ) + $this->getBaseConstraintOptions($groups)
            ),
        );
    }
}
