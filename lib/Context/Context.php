<?php

declare(strict_types=1);

namespace Netgen\Layouts\Context;

use ArrayIterator;
use Netgen\Layouts\Exception\Context\ContextException;
use Netgen\Layouts\Exception\RuntimeException;
use Traversable;

final class Context implements ContextInterface
{
    /**
     * @var array
     */
    private $contextVariables = [];

    public function set(string $variableName, $value): void
    {
        $this->contextVariables[$variableName] = $value;
    }

    public function add(array $context): void
    {
        $this->contextVariables = $context + $this->contextVariables;
    }

    public function has(string $variableName): bool
    {
        return array_key_exists($variableName, $this->contextVariables);
    }

    public function get(string $variableName)
    {
        if (!$this->has($variableName)) {
            throw ContextException::noVariable($variableName);
        }

        return $this->contextVariables[$variableName];
    }

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
     *
     * @return bool
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
