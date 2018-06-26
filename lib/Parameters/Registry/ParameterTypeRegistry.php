<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\Registry;

use ArrayIterator;
use Netgen\BlockManager\Exception\Parameters\ParameterTypeException;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Parameters\ParameterTypeInterface;

final class ParameterTypeRegistry implements ParameterTypeRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterTypeInterface[]
     */
    private $parameterTypes = [];

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterTypeInterface[]
     */
    private $parameterTypesByClass = [];

    public function __construct(ParameterTypeInterface ...$parameterTypes)
    {
        foreach ($parameterTypes as $parameterType) {
            $this->parameterTypes[$parameterType->getIdentifier()] = $parameterType;
            $this->parameterTypesByClass[get_class($parameterType)] = $parameterType;
        }
    }

    public function hasParameterType(string $identifier): bool
    {
        return isset($this->parameterTypes[$identifier]);
    }

    public function getParameterType(string $identifier): ParameterTypeInterface
    {
        if (!$this->hasParameterType($identifier)) {
            throw ParameterTypeException::noParameterType($identifier);
        }

        return $this->parameterTypes[$identifier];
    }

    public function getParameterTypeByClass(string $class): ParameterTypeInterface
    {
        if (!isset($this->parameterTypesByClass[$class])) {
            throw ParameterTypeException::noParameterTypeClass($class);
        }

        return $this->parameterTypesByClass[$class];
    }

    public function getParameterTypes(): array
    {
        return $this->parameterTypes;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->parameterTypes);
    }

    public function count()
    {
        return count($this->parameterTypes);
    }

    public function offsetExists($offset)
    {
        return $this->hasParameterType($offset);
    }

    public function offsetGet($offset)
    {
        return $this->getParameterType($offset);
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
