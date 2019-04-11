<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\Item\ValueType\ValueType;

interface ValueTypeRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Returns if registry has a value type.
     */
    public function hasValueType(string $identifier): bool;

    /**
     * Returns a value type for provided identifier.
     *
     * @throws \Netgen\Layouts\Exception\Item\ItemException If value type does not exist
     */
    public function getValueType(string $identifier): ValueType;

    /**
     * Returns all value types.
     *
     * @param bool $onlyEnabled
     *
     * @return \Netgen\Layouts\Item\ValueType\ValueType[]
     */
    public function getValueTypes(bool $onlyEnabled = false): array;
}
