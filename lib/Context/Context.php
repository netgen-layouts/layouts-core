<?php

declare(strict_types=1);

namespace Netgen\Layouts\Context;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\Exception\Context\ContextException;
use Netgen\Layouts\Exception\RuntimeException;
use Traversable;

use function array_key_exists;
use function count;

/**
 * @implements \IteratorAggregate<string, mixed>
 * @implements \ArrayAccess<string, mixed>
 */
final class Context implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * @var array<string, mixed>
     */
    private array $contextVariables = [];

    /**
     * Sets a variable to the context. Variable value needs to be
     * a scalar or an array/hash of scalars.
     *
     * @param mixed $value
     */
    public function set(string $variableName, $value): void
    {
        $this->contextVariables[$variableName] = $value;
    }

    /**
     * Adds the provided hash array of values to the context.
     *
     * This replaces already existing variables.
     *
     * @param array<string, mixed> $context
     */
    public function add(array $context): void
    {
        $this->contextVariables = $context + $this->contextVariables;
    }

    /**
     * Returns if the variable with provided name exists in the context.
     */
    public function has(string $variableName): bool
    {
        return array_key_exists($variableName, $this->contextVariables);
    }

    /**
     * Returns the variable with provided name from the context.
     *
     * @throws \Netgen\Layouts\Exception\Context\ContextException If variable with provided name does not exist
     *
     * @return mixed
     */
    public function get(string $variableName)
    {
        if (!$this->has($variableName)) {
            throw ContextException::noVariable($variableName);
        }

        return $this->contextVariables[$variableName];
    }

    /**
     * Returns all variables from the context.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->contextVariables;
    }

    public function count(): int
    {
        return count($this->contextVariables);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->contextVariables);
    }

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        throw new RuntimeException('Method call not supported.');
    }
}
