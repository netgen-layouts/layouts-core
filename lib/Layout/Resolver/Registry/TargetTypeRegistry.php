<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\Registry;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\Exception\Layout\TargetTypeException;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Layout\Resolver\TargetTypeInterface;
use Traversable;

use function array_filter;
use function array_key_exists;
use function count;

/**
 * @implements \ArrayAccess<string, \Netgen\Layouts\Layout\Resolver\TargetTypeInterface>
 * @implements \IteratorAggregate<string, \Netgen\Layouts\Layout\Resolver\TargetTypeInterface>
 */
final class TargetTypeRegistry implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * @var array<string, \Netgen\Layouts\Layout\Resolver\TargetTypeInterface>
     */
    private array $targetTypes;

    /**
     * @param iterable<string, \Netgen\Layouts\Layout\Resolver\TargetTypeInterface> $targetTypes
     */
    public function __construct(
        iterable $targetTypes,
    ) {
        $this->targetTypes = array_filter(
            [...$targetTypes],
            static fn (TargetTypeInterface $targetType): bool => true,
        );
    }

    /**
     * Returns if registry has a target type.
     */
    public function hasTargetType(string $type): bool
    {
        return array_key_exists($type, $this->targetTypes);
    }

    /**
     * Returns a target type with provided type.
     *
     * @throws \Netgen\Layouts\Exception\Layout\TargetTypeException If target type does not exist
     */
    public function getTargetType(string $type): TargetTypeInterface
    {
        if (!$this->hasTargetType($type)) {
            throw TargetTypeException::noTargetType($type);
        }

        return $this->targetTypes[$type];
    }

    /**
     * Returns all target types.
     *
     * @return array<string, \Netgen\Layouts\Layout\Resolver\TargetTypeInterface>
     */
    public function getTargetTypes(): array
    {
        return $this->targetTypes;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->targetTypes);
    }

    public function count(): int
    {
        return count($this->targetTypes);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->hasTargetType($offset);
    }

    public function offsetGet(mixed $offset): TargetTypeInterface
    {
        return $this->getTargetType($offset);
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
