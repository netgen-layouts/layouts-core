<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Item\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\BlockManager\Item\ValueType\ValueType;

interface ValueTypeRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Adds a value type to registry.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Item\ValueType\ValueType $valueType
     */
    public function addValueType($identifier, ValueType $valueType);

    /**
     * Returns if registry has a value type.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasValueType($identifier);

    /**
     * Returns a value type for provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Item\ItemException If value type does not exist
     *
     * @return \Netgen\BlockManager\Item\ValueType\ValueType
     */
    public function getValueType($identifier);

    /**
     * Returns all value types.
     *
     * @param bool $onlyEnabled
     *
     * @return \Netgen\BlockManager\Item\ValueType\ValueType[]
     */
    public function getValueTypes($onlyEnabled = false);
}
