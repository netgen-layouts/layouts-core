<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Registry;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\Exception\Layout\LayoutTypeException;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Layout\Type\LayoutTypeInterface;
use Traversable;

use function array_filter;
use function count;

/**
 * @implements \IteratorAggregate<string, \Netgen\Layouts\Layout\Type\LayoutTypeInterface>
 * @implements \ArrayAccess<string, \Netgen\Layouts\Layout\Type\LayoutTypeInterface>
 */
final class LayoutTypeRegistry implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * @param array<string, \Netgen\Layouts\Layout\Type\LayoutTypeInterface> $layoutTypes
     */
    public function __construct(
        private array $layoutTypes,
    ) {
        $this->layoutTypes = array_filter(
            $this->layoutTypes,
            static fn (LayoutTypeInterface $layoutType): bool => true,
        );
    }

    /**
     * Returns if registry has a layout type.
     */
    public function hasLayoutType(string $identifier): bool
    {
        return isset($this->layoutTypes[$identifier]);
    }

    /**
     * Returns the layout type with provided identifier.
     *
     * @throws \Netgen\Layouts\Exception\Layout\LayoutTypeException If layout type with provided identifier does not exist
     */
    public function getLayoutType(string $identifier): LayoutTypeInterface
    {
        if (!$this->hasLayoutType($identifier)) {
            throw LayoutTypeException::noLayoutType($identifier);
        }

        return $this->layoutTypes[$identifier];
    }

    /**
     * Returns all layout types.
     *
     * @return array<string, \Netgen\Layouts\Layout\Type\LayoutTypeInterface>
     */
    public function getLayoutTypes(bool $onlyEnabled = false): array
    {
        if (!$onlyEnabled) {
            return $this->layoutTypes;
        }

        return array_filter(
            $this->layoutTypes,
            static fn (LayoutTypeInterface $layoutType): bool => $layoutType->isEnabled(),
        );
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->layoutTypes);
    }

    public function count(): int
    {
        return count($this->layoutTypes);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->hasLayoutType($offset);
    }

    public function offsetGet(mixed $offset): LayoutTypeInterface
    {
        return $this->getLayoutType($offset);
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
