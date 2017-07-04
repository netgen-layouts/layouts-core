<?php

namespace Netgen\BlockManager\Parameters\ParameterType\Compound;

use Netgen\BlockManager\Parameters\CompoundParameterType;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class BooleanType extends CompoundParameterType
{
    /**
     * Returns the parameter type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'compound_boolean';
    }

    /**
     * Configures the options for this parameter.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setRequired(array('reverse'));
        $optionsResolver->setAllowedTypes('reverse', 'bool');
        $optionsResolver->setDefault('reverse', false);

        $optionsResolver->setDefault('default_value', function (Options $options, $previousValue) {
            if ($options['required']) {
                return false;
            }

            return $previousValue;
        });
    }

    /**
     * Returns if the parameter value is empty.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param mixed $value
     *
     * @return bool
     */
    public function isValueEmpty(ParameterInterface $parameter, $value)
    {
        return $value === null;
    }

    /**
     * Returns constraints that will be used when parameter is required.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param mixed $value
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    protected function getRequiredConstraints(ParameterInterface $parameter, $value)
    {
        if ($parameter->isRequired()) {
            return array(
                new Constraints\NotNull(),
            );
        }

        return array();
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
            new Constraints\Type(
                array(
                    'type' => 'bool',
                )
            ),
        );
    }
}
