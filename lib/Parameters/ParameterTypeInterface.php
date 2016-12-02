<?php

namespace Netgen\BlockManager\Parameters;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface ParameterTypeInterface
{
    /**
     * Returns the parameter type identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Configures the options for this parameter.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    public function configureOptions(OptionsResolver $optionsResolver);

    /**
     * Returns the parameter constraints.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param mixed $value
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints(ParameterInterface $parameter, $value);

    /**
     * Converts the parameter value to from a domain format to scalar/hash format.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function toHash($value);

    /**
     * Converts the provided parameter value to value usable by the domain.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function fromHash($value);

    /**
     * Potentially converts the input value to value usable by the domain.
     *
     * If the value cannot be converted, original value should be returned.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function createValueFromInput($value);

    /**
     * Returns if the parameter value is empty.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isValueEmpty($value);
}
