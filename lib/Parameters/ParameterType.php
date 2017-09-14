<?php

namespace Netgen\BlockManager\Parameters;

use Netgen\BlockManager\Exception\Parameters\ParameterTypeException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

abstract class ParameterType implements ParameterTypeInterface
{
    abstract public function getIdentifier();

    public function configureOptions(OptionsResolver $optionsResolver)
    {
    }

    public function getConstraints(ParameterInterface $parameter, $value)
    {
        if ($parameter->getType()->getIdentifier() !== $this->getIdentifier()) {
            throw ParameterTypeException::unsupportedParameterType(
                $parameter->getType()->getIdentifier()
            );
        }

        return array_merge(
            $this->getRequiredConstraints($parameter, $value),
            $this->getValueConstraints($parameter, $value)
        );
    }

    /**
     * Converts the parameter value from a domain format to scalar/hash format.
     *
     * This is a trivial implementation, just returning the provided value, usable by parameters
     * which have the scalar/hash format equal to domain format.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param mixed $value
     *
     * @return mixed
     */
    public function toHash(ParameterInterface $parameter, $value)
    {
        return $value;
    }

    /**
     * Converts the provided parameter value to value usable by the domain.
     *
     * This is a trivial implementation, just returning the provided value, usable by parameters
     * which have the scalar/hash format equal to domain format.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param mixed $value
     *
     * @return mixed
     */
    public function fromHash(ParameterInterface $parameter, $value)
    {
        return $value;
    }

    public function isValueEmpty(ParameterInterface $parameter, $value)
    {
        return empty($value);
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
                new Constraints\NotBlank(),
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
    abstract protected function getValueConstraints(ParameterInterface $parameter, $value);
}
