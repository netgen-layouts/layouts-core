<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Symfony\Component\Validator\Constraints;

class Integer extends ParameterType
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'integer';
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
        $options = $parameterDefinition->getOptions();

        $constraints = array(
            new Constraints\Type(
                array(
                    'type' => 'int',
                )
            ),
        );

        if ($options['min'] !== null) {
            $constraints[] = new Constraints\GreaterThanOrEqual(
                array('value' => $options['min'])
            );
        }

        if ($options['max'] !== null) {
            $constraints[] = new Constraints\LessThanOrEqual(
                array('value' => $options['max'])
            );
        }

        return $constraints;
    }
}
