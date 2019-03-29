<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Item\Registry;

use ArrayIterator;
use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Item\ValueType\ValueType;
use Traversable;

final class ValueTypeRegistry implements ValueTypeRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Item\ValueType\ValueType[]
     */
    private $valueTypes;

    /**
     * @param \Netgen\BlockManager\Item\ValueType\ValueType[] $valueTypes
     */
    public function __construct(array $valueTypes)
    {
        $this->valueTypes = array_filter(
            $valueTypes,
            function (ValueType $valueType): bool {
                return true;
            }
        );
    }

    public function hasValueType(string $identifier): bool
    {
        return isset($this->valueTypes[$identifier]);
    }

    public function getValueType(string $identifier): ValueType
    {
        if (!$this->hasValueType($identifier)) {
            throw ItemException::noValueType($identifier);
        }

        return $this->valueTypes[$identifier];
    }

    public function getValueTypes(bool $onlyEnabled = false): array
    {
        if (!$onlyEnabled) {
            return $this->valueTypes;
        }

        return array_filter(
            $this->valueTypes,
            function (ValueType $valueType): bool {
                return $valueType->isEnabled();
            }
        );
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->valueTypes);
    }

    public function count(): int
    {
        return count($this->valueTypes);
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->hasValueType($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getValueType($offset);
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
