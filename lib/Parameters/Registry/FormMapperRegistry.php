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

    public function addFormMapper($parameterType, MapperInterface $formMapper)
    {
        $this->formMappers[$parameterType] = $formMapper;
    }

    public function hasFormMapper($parameterType)
    {
        return isset($this->formMappers[$parameterType]);
    }

    public function getFormMapper($parameterType)
    {
        if (!$this->hasFormMapper($parameterType)) {
            throw ParameterTypeException::noFormMapper($parameterType);
        }

        return $this->formMappers[$parameterType];
    }

    public function getFormMappers()
    {
        return $this->formMappers;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->formMappers);
    }

    public function count()
    {
        return count($this->formMappers);
    }

    public function offsetExists($offset)
    {
        return $this->hasFormMapper($offset);
    }

    public function offsetGet($offset)
    {
        return $this->getFormMapper($offset);
    }

    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('Method call not supported.');
    }

    public function offsetUnset($offset)
    {
        throw new RuntimeException('Method call not supported.');
    }
}
