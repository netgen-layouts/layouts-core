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
     * Returns if registry has a value type.
     */
    public function hasValueType(string $identifier): bool;

    /**
     * Returns a value type for provided identifier.
     *
     * @throws \Netgen\BlockManager\Exception\Item\ItemException If value type does not exist
     */
    public function getValueType(string $identifier): ValueType;

    /**
     * Returns all value types.
     *
     * @param bool $onlyEnabled
     *
     * @return \Netgen\BlockManager\Item\ValueType\ValueType[]
     */
    public function getValueTypes(bool $onlyEnabled = false): array;
}
