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

/**
 * @implements \IteratorAggregate<string, \Netgen\Layouts\Layout\Resolver\ConditionTypeInterface>
 * @implements \ArrayAccess<string, \Netgen\Layouts\Layout\Resolver\ConditionTypeInterface>
 */
final class ConditionTypeRegistry implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * @var array<string, \Netgen\Layouts\Layout\Resolver\ConditionTypeInterface>
     */
    private $conditionTypes = [];

    /**
     * @param iterable<string, \Netgen\Layouts\Layout\Resolver\ConditionTypeInterface> $conditionTypes
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
        return isset($this->conditionTypes[$type]);
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

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->hasConditionType($offset);
    }

    /**
     * @param mixed $offset
     */
    public function offsetGet($offset): ConditionTypeInterface
    {
        return $this->getConditionType($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new RuntimeException('Method call not supported.');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        throw new RuntimeException('Method call not supported.');
    }
}
