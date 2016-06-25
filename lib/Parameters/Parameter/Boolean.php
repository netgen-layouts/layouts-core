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
     * Returns the default parameter value.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        if ($this->isRequired && $this->defaultValue === null) {
            return false;
        }

        return parent::getDefaultValue();
    }

    /**
     * Returns constraints that will be used when parameter is required.
     *
     * Overriden because base NotBlank constraint checks for `false` too.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getRequiredConstraints()
    {
        if ($this->isRequired()) {
            return array(
                new Constraints\NotNull(),
            );
        }

        return array();
    }

    /**
     * Returns constraints that will be used to validate the parameter value.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getValueConstraints()
    {
        return array(
            new Constraints\Type(
                array(
                    'type' => 'bool',
                )
            ),
        );
    }
}
