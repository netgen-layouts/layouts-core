<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Registry;

use ArrayIterator;
use Netgen\BlockManager\Exception\Layout\LayoutTypeException;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Layout\Type\LayoutTypeInterface;
use Traversable;

final class LayoutTypeRegistry implements LayoutTypeRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Layout\Type\LayoutTypeInterface[]
     */
    private $layoutTypes;

    /**
     * @param \Netgen\BlockManager\Layout\Type\LayoutTypeInterface[] $layoutTypes
     */
    public function __construct(array $layoutTypes)
    {
        $this->layoutTypes = array_filter(
            $layoutTypes,
            function (LayoutTypeInterface $layoutType): bool {
                return true;
            }
        );
    }

    public function hasLayoutType(string $identifier): bool
    {
        return isset($this->layoutTypes[$identifier]);
    }

    public function getLayoutType(string $identifier): LayoutTypeInterface
    {
        if (!$this->hasLayoutType($identifier)) {
            throw LayoutTypeException::noLayoutType($identifier);
        }

        return $this->layoutTypes[$identifier];
    }

    public function getLayoutTypes(bool $onlyEnabled = false): array
    {
        if (!$onlyEnabled) {
            return $this->layoutTypes;
        }

        return array_filter(
            $this->layoutTypes,
            function (LayoutTypeInterface $layoutType): bool {
                return $layoutType->isEnabled();
            }
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
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->hasLayoutType($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
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
