<?php

namespace Netgen\BlockManager\Parameters\Registry;

use ArrayIterator;
use Netgen\BlockManager\Exception\Parameters\ParameterTypeException;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Parameters\Form\MapperInterface;

class FormMapperRegistry implements FormMapperRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\MapperInterface[]
     */
    protected $formMappers = array();

    /**
     * Adds a parameter form mapper to registry.
     *
     * @param string $parameterType
     * @param \Netgen\BlockManager\Parameters\Form\MapperInterface $formMapper
     */
    public function addFormMapper($parameterType, MapperInterface $formMapper)
    {
        $this->formMappers[$parameterType] = $formMapper;
    }

    /**
     * Returns if registry has a parameter form mapper.
     *
     * @param string $parameterType
     *
     * @return bool
     */
    public function hasFormMapper($parameterType)
    {
        return isset($this->formMappers[$parameterType]);
    }

    /**
     * Returns a form mapper for provided parameter type.
     *
     * @param string $parameterType
     *
     * @throws \Netgen\BlockManager\Exception\Parameters\ParameterTypeException If form mapper does not exist
     *
     * @return \Netgen\BlockManager\Parameters\Form\MapperInterface
     */
    public function getFormMapper($parameterType)
    {
        if (!$this->hasFormMapper($parameterType)) {
            throw ParameterTypeException::noFormMapper($parameterType);
        }

        return $this->formMappers[$parameterType];
    }

    /**
     * Returns all form mappers.
     *
     * @return \Netgen\BlockManager\Parameters\Form\MapperInterface[]
     */
    public function getFormMappers()
    {
        return $this->formMappers;
    }

    /**
     * Retrieve an external iterator.
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->formMappers);
    }

    /**
     * Count elements of an object.
     *
     * @return int
     */
    public function count()
    {
        return count($this->formMappers);
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
        return $this->hasFormMapper($offset);
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
        return $this->getFormMapper($offset);
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
