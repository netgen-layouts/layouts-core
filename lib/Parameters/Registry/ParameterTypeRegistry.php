<?php

namespace Netgen\BlockManager\Parameters\Registry;

use ArrayIterator;
use Netgen\BlockManager\Exception\Parameters\ParameterTypeException;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Parameters\ParameterTypeInterface;

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
     * @throws \Netgen\BlockManager\Exception\Parameters\ParameterTypeException If parameter type does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    public function getParameterType($identifier)
    {
        if (!$this->hasParameterType($identifier)) {
            throw ParameterTypeException::noParameterType($identifier);
        }

        return $this->parameterTypes[$identifier];
    }

    /**
     * Returns a parameter type with provided class.
     *
     * @param string $class
     *
     * @throws \Netgen\BlockManager\Exception\Parameters\ParameterTypeException If parameter type does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    public function getParameterTypeByClass($class)
    {
        if (!isset($this->parameterTypesByClass[$class])) {
            throw ParameterTypeException::noParameterTypeClass($class);
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

    /**
     * Retrieve an external iterator.
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->parameterTypes);
    }

    /**
     * Count elements of an object.
     *
     * @return int
     */
    public function count()
    {
        return count($this->parameterTypes);
    }

    /**
     * Whether a offset exists.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->hasParameterType($offset);
    }

    /**
     * Offset to retrieve.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getParameterType($offset);
    }

    /**
     * Offset to set.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('Method call not supported.');
    }

    /**
     * Offset to unset.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        throw new RuntimeException('Method call not supported.');
    }
}
