<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\Registry;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\Exception\Layout\ConditionTypeException;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Layout\Resolver\ConditionTypeInterface;
use Traversable;

use function array_key_exists;
use function count;

/**
 * @implements \ArrayAccess<string, \Netgen\Layouts\Layout\Resolver\ConditionTypeInterface>
 * @implements \IteratorAggregate<string, \Netgen\Layouts\Layout\Resolver\ConditionTypeInterface>
 */
final class ConditionTypeRegistry implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * @var array<string, \Netgen\Layouts\Layout\Resolver\ConditionTypeInterface>
     */
    private array $conditionTypes = [];

    /**
     * @param iterable<int, \Netgen\Layouts\Layout\Resolver\ConditionTypeInterface> $conditionTypes
     */
    public function __construct(iterable $conditionTypes)
    {
        foreach ($conditionTypes as $conditionType) {
            if ($conditionType instanceof ConditionTypeInterface) {
                $this->conditionTypes[$conditionType::getType()] = $conditionType;
            }
        }
    }

    /**
     * Returns if registry has a condition type.
     */
    public function hasConditionType(string $type): bool
    {
        return array_key_exists($type, $this->conditionTypes);
    }

    /**
     * Returns a condition type with provided type.
     *
     * @throws \Netgen\Layouts\Exception\Layout\ConditionTypeException If condition type does not exist
     */
    public function getConditionType(string $type): ConditionTypeInterface
    {
        if (!$this->hasConditionType($type)) {
            throw ConditionTypeException::noConditionType($type);
        }

        return $this->conditionTypes[$type];
    }

    /**
     * Returns all condition types.
     *
     * @return array<string, \Netgen\Layouts\Layout\Resolver\ConditionTypeInterface>
     */
    public function getConditionTypes(): array
    {
        return $this->conditionTypes;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->conditionTypes);
    }

    public function count(): int
    {
        return count($this->conditionTypes);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->hasConditionType($offset);
    }

    public function offsetGet(mixed $offset): ConditionTypeInterface
    {
        return $this->getConditionType($offset);
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
