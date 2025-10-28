<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\Registry;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\Exception\Layout\ConditionTypeException;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Layout\Resolver\ConditionType;
use Netgen\Layouts\Layout\Resolver\ConditionTypeInterface;
use Traversable;

use function count;
use function method_exists;
use function trigger_deprecation;

/**
 * @implements \IteratorAggregate<string, \Netgen\Layouts\Layout\Resolver\ConditionTypeInterface>
 * @implements \ArrayAccess<string, \Netgen\Layouts\Layout\Resolver\ConditionTypeInterface>
 */
final class ConditionTypeRegistry implements IteratorAggregate, Countable, ArrayAccess
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
                if (!method_exists($conditionType, 'export')) {
                    trigger_deprecation('netgen/layouts-core', '1.2', 'Implementing "%s" interface without implementing "%s" method is deprecated. Implement the method or extend the "%s" abstract class in your condition type.', ConditionTypeInterface::class, 'export', ConditionType::class);
                }

                if (!method_exists($conditionType, 'import')) {
                    trigger_deprecation('netgen/layouts-core', '1.2', 'Implementing "%s" interface without implementing "%s" method is deprecated. Implement the method or extend the "%s" abstract class in your condition type.', ConditionTypeInterface::class, 'import', ConditionType::class);
                }

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

    public function offsetExists(mixed $offset): bool
    {
        return $this->hasConditionType($offset);
    }

    public function offsetGet(mixed $offset): ConditionTypeInterface
    {
        return $this->getConditionType($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new RuntimeException('Method call not supported.');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new RuntimeException('Method call not supported.');
    }
}
