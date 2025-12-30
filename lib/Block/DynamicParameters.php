<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block;

use ArrayAccess;
use ArrayIterator;
use Closure;
use Countable;
use IteratorAggregate;
use Traversable;

use function array_key_exists;
use function count;

/**
 * @implements \ArrayAccess<string, mixed>
 * @implements \IteratorAggregate<string, mixed>
 */
final class DynamicParameters implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * @var array<string, mixed>
     */
    private array $dynamicParameters = [];

    public function count(): int
    {
        return count($this->dynamicParameters);
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->dynamicParameters);
    }

    public function offsetGet(mixed $offset): mixed
    {
        if (!$this->offsetExists($offset)) {
            return null;
        }

        if (!$this->dynamicParameters[$offset] instanceof Closure) {
            return $this->dynamicParameters[$offset];
        }

        return $this->dynamicParameters[$offset]();
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->dynamicParameters[(string) $offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        if (!$this->offsetExists($offset)) {
            return;
        }

        unset($this->dynamicParameters[$offset]);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->dynamicParameters);
    }
}
