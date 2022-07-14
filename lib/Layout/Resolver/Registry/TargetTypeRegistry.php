<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\Registry;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\Exception\Layout\TargetTypeException;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Layout\Resolver\TargetType;
use Netgen\Layouts\Layout\Resolver\TargetTypeInterface;
use Traversable;

use function count;
use function method_exists;
use function trigger_deprecation;

/**
 * @implements \IteratorAggregate<string, \Netgen\Layouts\Layout\Resolver\TargetTypeInterface>
 * @implements \ArrayAccess<string, \Netgen\Layouts\Layout\Resolver\TargetTypeInterface>
 */
final class TargetTypeRegistry implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * @var array<string, \Netgen\Layouts\Layout\Resolver\TargetTypeInterface>
     */
    private array $targetTypes = [];

    /**
     * @param iterable<int, \Netgen\Layouts\Layout\Resolver\TargetTypeInterface> $targetTypes
     */
    public function __construct(iterable $targetTypes)
    {
        foreach ($targetTypes as $targetType) {
            if ($targetType instanceof TargetTypeInterface) {
                if (!method_exists($targetType, 'export')) {
                    trigger_deprecation('netgen/layouts-core', '1.2', 'Implementing "%s" interface without implementing "%s" method is deprecated. Implement the method or extend the "%s" abstract class in your target type.', TargetTypeInterface::class, 'export', TargetType::class);
                }

                if (!method_exists($targetType, 'import')) {
                    trigger_deprecation('netgen/layouts-core', '1.2', 'Implementing "%s" interface without implementing "%s" method is deprecated. Implement the method or extend the "%s" abstract class in your target type.', TargetTypeInterface::class, 'import', TargetType::class);
                }

                $this->targetTypes[$targetType::getType()] = $targetType;
            }
        }
    }

    /**
     * Returns if registry has a target type.
     */
    public function hasTargetType(string $type): bool
    {
        return isset($this->targetTypes[$type]);
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

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return $this->hasTargetType($offset);
    }

    /**
     * @param mixed $offset
     */
    public function offsetGet($offset): TargetTypeInterface
    {
        return $this->getTargetType($offset);
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
