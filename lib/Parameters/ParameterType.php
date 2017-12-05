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

    /**
     * Returns the parameter value converted to a format suitable for exporting.
     *
     * This is useful if exported value is different from a stored value, for example
     * when exporting IDs from an external CMS which need to be exported not as IDs
     * but as remote IDs.
     *
     * This is a trivial implementation that returns the value in the same format as
     * self::toHash(). Overriden implementations should take care to retain this behaviour.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param mixed $value
     *
     * @return mixed
     */
    public function export(ParameterInterface $parameter, $value)
    {
        return $this->toHash($parameter, $value);
    }

    /**
     * Returns the parameter value converted from the exported format.
     *
     * This is useful if stored value is different from an exported value, for example
     * when importing IDs from an external CMS which need to be imported as database IDs
     * in contrast to some kind of remote ID which would be stored in the export.
     *
     * This is a trivial implementation that returns the value in the same format as
     * self::fromHash(). Overriden implementations should take care to retain this behaviour.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param mixed $value
     *
     * @return mixed
     */
    public function import(ParameterInterface $parameter, $value)
    {
        return $this->fromHash($parameter, $value);
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
