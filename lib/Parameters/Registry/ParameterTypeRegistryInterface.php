<?php

namespace Netgen\BlockManager\Parameters\Registry;

use Netgen\BlockManager\Parameters\ParameterTypeInterface;

interface ParameterTypeRegistryInterface
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
     * @param string $type
     *
     * @return bool
     */
    public function hasParameterType($type);

    /**
     * Returns a parameter type with provided identifier.
     *
     * @param string $type
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If parameter type does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    public function getParameterType($type);

    /**
     * Returns all parameter types.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterTypeInterface[]
     */
    public function getParameterTypes();
}
