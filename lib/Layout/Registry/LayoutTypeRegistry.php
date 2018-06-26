<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Registry;

use ArrayIterator;
use Netgen\BlockManager\Exception\Layout\LayoutTypeException;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Layout\Type\LayoutTypeInterface;

final class LayoutTypeRegistry implements LayoutTypeRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Layout\Type\LayoutTypeInterface[]
     */
    private $layoutTypes = [];

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

    public function getIterator()
    {
        return new ArrayIterator($this->layoutTypes);
    }

    public function count()
    {
        return count($this->layoutTypes);
    }

    public function offsetExists($offset)
    {
        return $this->hasLayoutType($offset);
    }

    public function offsetGet($offset)
    {
        return $this->getLayoutType($offset);
    }

    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('Method call not supported.');
    }

    public function offsetUnset($offset)
    {
        throw new RuntimeException('Method call not supported.');
    }
}
