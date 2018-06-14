<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Context;

use ArrayIterator;
use Netgen\BlockManager\Exception\Context\ContextException;
use Netgen\BlockManager\Exception\RuntimeException;

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

    public function count()
    {
        return count($this->contextVariables);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->contextVariables);
    }

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        throw new RuntimeException('Method call not supported.');
    }
}
