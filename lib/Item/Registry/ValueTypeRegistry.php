<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Item\Registry;

use ArrayIterator;
use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Item\ValueType\ValueType;

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

    public function getIterator()
    {
        return new ArrayIterator($this->valueTypes);
    }

    public function count()
    {
        return count($this->valueTypes);
    }

    public function offsetExists($offset)
    {
        return $this->hasValueType($offset);
    }

    public function offsetGet($offset)
    {
        return $this->getValueType($offset);
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
