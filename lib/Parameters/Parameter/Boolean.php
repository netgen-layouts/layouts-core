<?php

namespace Netgen\BlockManager\Parameters\Parameter;

use Netgen\BlockManager\Parameters\Parameter;
use Symfony\Component\Validator\Constraints;

class Boolean extends Parameter
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'boolean';
    }

    /**
     * Returns constraints that are common to all parameters.
     *
     * Overriden because base NotBlank constraint checks for `false` too.
     *
     * @param array $groups
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getBaseConstraints(array $groups = null)
    {
        if ($this->isRequired()) {
            return array(
                new Constraints\NotNull(
                    $this->getBaseConstraintOptions($groups)
                ),
            );
        }

        return array();
    }

    /**
     * Returns constraints that are specific to parameter.
     *
     * @param array $groups
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getParameterConstraints(array $groups = null)
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
