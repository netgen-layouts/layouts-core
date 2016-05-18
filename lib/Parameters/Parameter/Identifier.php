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
     * Returns constraints that are specific to parameter.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    protected function getParameterConstraints()
    {
        return array(
            new Constraints\Regex(
                array(
                    'pattern' => '/[A-Za-z0-9_]+/'
                )
            ),
        );
    }
}
