<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\Validator\Constraints;

class IdentifierType extends ParameterType
{
    /**
     * getIdentifierReturns the parameter type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'identifier';
    }

    /**
     * Returns constraints that will be used to validate the parameter value.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param mixed $value
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    protected function getValueConstraints(ParameterInterface $parameter, $value)
    {
        return array(
            new Constraints\Regex(
                array(
                    'pattern' => '/^[A-Za-z]([A-Za-z0-9_])*$/',
                )
            ),
        );
    }
}
