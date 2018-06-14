<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\Registry;

use ArrayIterator;
use Netgen\BlockManager\Exception\Parameters\ParameterTypeException;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Parameters\Form\MapperInterface;

final class FormMapperRegistry implements FormMapperRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\MapperInterface[]
     */
    private $formMappers = [];

    public function addFormMapper(string $parameterType, MapperInterface $formMapper): void
    {
        $this->formMappers[$parameterType] = $formMapper;
    }

    public function hasFormMapper(string $parameterType): bool
    {
        return isset($this->formMappers[$parameterType]);
    }

    public function getFormMapper(string $parameterType): MapperInterface
    {
        if (!$this->hasFormMapper($parameterType)) {
            throw ParameterTypeException::noFormMapper($parameterType);
        }

        return $this->formMappers[$parameterType];
    }

    public function getFormMappers(): array
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
