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
     * @var array<string, \Netgen\Layouts\Layout\Type\LayoutTypeInterface>
     */
    private array $layoutTypes;

    /**
     * @param array<string, \Netgen\Layouts\Layout\Type\LayoutTypeInterface> $layoutTypes
     */
    public function __construct(array $layoutTypes)
    {
        $this->layoutTypes = array_filter(
            $layoutTypes,
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

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return $this->hasLayoutType($offset);
    }

    /**
     * @param mixed $offset
     */
    public function offsetGet($offset): LayoutTypeInterface
    {
        return $this->getLayoutType($offset);
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
