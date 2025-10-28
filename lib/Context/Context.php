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
     */
    public function set(string $variableName, mixed $value): void
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
     */
    public function get(string $variableName): mixed
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

    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set((string) $offset, $value);
    }

    public function offsetUnset(mixed $offset): never
    {
        throw new RuntimeException('Method call not supported.');
    }
}
