<?php

namespace Netgen\BlockManager\Parameters\Registry;

use Netgen\BlockManager\Parameters\ParameterTypeInterface;
use Netgen\BlockManager\Exception\InvalidArgumentException;

class ParameterTypeRegistry implements ParameterTypeRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterTypeInterface[]
     */
    protected $parameterTypes = array();

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterTypeInterface[]
     */
    protected $parameterTypesByClass = array();

    /**
     * Adds a parameter type to registry.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterTypeInterface $parameterType
     */
    public function addParameterType(ParameterTypeInterface $parameterType)
    {
        $this->parameterTypes[$parameterType->getIdentifier()] = $parameterType;
        $this->parameterTypesByClass[get_class($parameterType)] = $parameterType;
    }

    /**
     * Returns if registry has a parameter type.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasParameterType($identifier)
    {
        return isset($this->parameterTypes[$identifier]);
    }

    /**
     * Returns a parameter type with provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If parameter type does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    public function getParameterType($identifier)
    {
        if (!$this->hasParameterType($identifier)) {
            throw new InvalidArgumentException(
                'type',
                sprintf(
                    'Parameter type with "%s" identifier does not exist.',
                    $identifier
                )
            );
        }

        return $this->parameterTypes[$identifier];
    }

    /**
     * Returns a parameter type with provided class.
     *
     * @param string $class
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If parameter type does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    public function getParameterTypeByClass($class)
    {
        if (!isset($this->parameterTypesByClass[$class])) {
            throw new InvalidArgumentException(
                'class',
                sprintf(
                    'Parameter type with class "%s" does not exist.',
                    $class
                )
            );
        }

        return $this->parameterTypesByClass[$class];
    }

    /**
     * Returns all parameter types.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterTypeInterface[]
     */
    public function getParameterTypes()
    {
        return $this->parameterTypes;
    }
}
