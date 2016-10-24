<?php

namespace Netgen\BlockManager\Parameters\Parameter;

use Netgen\BlockManager\Parameters\Parameter;
use Symfony\Component\Validator\Constraints;

class Identifier extends Parameter
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'identifier';
    }

    /**
     * Returns constraints that will be used to validate the parameter value.
     *
     * @param mixed $value
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getValueConstraints($value)
    {
        return array(
            new Constraints\Regex(
                array(
                    'pattern' => '/^[A-Za-z0-9_]+$/',
                )
            ),
        );
    }
}
