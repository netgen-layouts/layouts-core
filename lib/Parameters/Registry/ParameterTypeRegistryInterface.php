<?php

namespace Netgen\BlockManager\Parameters\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\BlockManager\Parameters\ParameterTypeInterface;

interface ParameterTypeRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Adds a parameter type to registry.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterTypeInterface $parameterType
     */
    public function addParameterType(ParameterTypeInterface $parameterType);

    /**
     * Returns if registry has a parameter type.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasParameterType($identifier);

    /**
     * Returns a parameter type with provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Parameters\ParameterTypeException If parameter type does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    public function getParameterType($identifier);

    /**
     * Returns a parameter type with provided class.
     *
     * @param string $class
     *
     * @throws \Netgen\BlockManager\Exception\Parameters\ParameterTypeException If parameter type does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    public function getParameterTypeByClass($class);

    /**
     * Returns all parameter types.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterTypeInterface[]
     */
    public function getParameterTypes();
}
