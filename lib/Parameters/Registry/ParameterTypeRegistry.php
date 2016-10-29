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
     * Adds a parameter type to registry.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterTypeInterface $parameterType
     */
    public function addParameterType(ParameterTypeInterface $parameterType)
    {
        $this->parameterTypes[$parameterType->getType()] = $parameterType;
    }

    /**
     * Returns if registry has a parameter type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function hasParameterType($type)
    {
        return isset($this->parameterTypes[$type]);
    }

    /**
     * Returns a parameter type with provided identifier.
     *
     * @param string $type
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If parameter type does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    public function getParameterType($type)
    {
        if (!$this->hasParameterType($type)) {
            throw new InvalidArgumentException(
                'type',
                sprintf(
                    'Parameter type "%s" does not exist.',
                    $type
                )
            );
        }

        return $this->parameterTypes[$type];
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
