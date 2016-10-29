<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Symfony\Component\Validator\Constraints;

class Choice extends ParameterType
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'choice';
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

        return array(
            new Constraints\Choice(
                array(
                    'choices' => array_values(
                        is_callable($options['options']) ?
                            $options['options']() :
                            $options['options']
                        ),
                    'multiple' => $options['multiple'],
                    'strict' => true,
                )
            ),
        );
    }
}
