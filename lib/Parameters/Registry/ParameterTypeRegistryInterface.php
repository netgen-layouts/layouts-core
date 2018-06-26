<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\BlockManager\Parameters\ParameterTypeInterface;

interface ParameterTypeRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Returns if registry has a parameter type.
     */
    public function hasParameterType(string $identifier): bool;

    /**
     * Returns a parameter type with provided identifier.
     *
     * @throws \Netgen\BlockManager\Exception\Parameters\ParameterTypeException If parameter type does not exist
     */
    public function getParameterType(string $identifier): ParameterTypeInterface;

    /**
     * Returns a parameter type with provided class.
     *
     * @throws \Netgen\BlockManager\Exception\Parameters\ParameterTypeException If parameter type does not exist
     */
    public function getParameterTypeByClass(string $class): ParameterTypeInterface;

    /**
     * Returns all parameter types.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterTypeInterface[]
     */
    public function getParameterTypes(): array;
}
