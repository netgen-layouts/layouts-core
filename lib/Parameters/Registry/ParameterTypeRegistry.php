<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Registry;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\Exception\Parameters\ParameterTypeException;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Parameters\ParameterTypeInterface;
use Traversable;

use function array_key_exists;
use function count;

/**
 * @implements \IteratorAggregate<string, \Netgen\Layouts\Parameters\ParameterTypeInterface>
 * @implements \ArrayAccess<string, \Netgen\Layouts\Parameters\ParameterTypeInterface>
 */
final class ParameterTypeRegistry implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * @var array<string, \Netgen\Layouts\Parameters\ParameterTypeInterface>
     */
    private array $parameterTypes = [];

    /**
     * @var array<class-string, \Netgen\Layouts\Parameters\ParameterTypeInterface>
     */
    private array $parameterTypesByClass = [];

    /**
     * @param iterable<\Netgen\Layouts\Parameters\ParameterTypeInterface> $parameterTypes
     */
    public function __construct(iterable $parameterTypes)
    {
        foreach ($parameterTypes as $parameterType) {
            if ($parameterType instanceof ParameterTypeInterface) {
                $this->parameterTypes[$parameterType::getIdentifier()] = $parameterType;
                $this->parameterTypesByClass[$parameterType::class] = $parameterType;
            }
        }
    }

    /**
     * Returns if registry has a parameter type.
     */
    public function hasParameterType(string $identifier): bool
    {
        return array_key_exists($identifier, $this->parameterTypes);
    }

    /**
     * Returns a parameter type with provided identifier.
     *
     * @throws \Netgen\Layouts\Exception\Parameters\ParameterTypeException If parameter type does not exist
     */
    public function getParameterType(string $identifier): ParameterTypeInterface
    {
        if (!$this->hasParameterType($identifier)) {
            throw ParameterTypeException::noParameterType($identifier);
        }

        return $this->parameterTypes[$identifier];
    }

    /**
     * Returns if registry has a parameter type with provided class.
     */
    public function hasParameterTypeByClass(string $class): bool
    {
        return array_key_exists($class, $this->parameterTypesByClass);
    }

    /**
     * Returns a parameter type with provided class.
     *
     * @throws \Netgen\Layouts\Exception\Parameters\ParameterTypeException If parameter type does not exist
     */
    public function getParameterTypeByClass(string $class): ParameterTypeInterface
    {
        if (!$this->hasParameterTypeByClass($class)) {
            throw ParameterTypeException::noParameterTypeClass($class);
        }

        return $this->parameterTypesByClass[$class];
    }

    /**
     * Returns all parameter types.
     *
     * @return array<string, \Netgen\Layouts\Parameters\ParameterTypeInterface>
     */
    public function getParameterTypes(): array
    {
        return $this->parameterTypes;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->parameterTypes);
    }

    public function count(): int
    {
        return count($this->parameterTypes);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->hasParameterType($offset);
    }

    public function offsetGet(mixed $offset): ParameterTypeInterface
    {
        return $this->getParameterType($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): never
    {
        throw new RuntimeException('Method call not supported.');
    }

    public function offsetUnset(mixed $offset): never
    {
        throw new RuntimeException('Method call not supported.');
    }
}
