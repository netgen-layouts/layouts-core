<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\Parameters\ParameterTypeInterface;

interface ParameterTypeRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Returns if registry has a parameter type.
     */
    public function hasParameterType(string $identifier): bool;

    /**
     * Returns a parameter type with provided identifier.
     *
     * @throws \Netgen\Layouts\Exception\Parameters\ParameterTypeException If parameter type does not exist
     */
    public function getParameterType(string $identifier): ParameterTypeInterface;

    /**
     * Returns a parameter type with provided class.
     *
     * @throws \Netgen\Layouts\Exception\Parameters\ParameterTypeException If parameter type does not exist
     */
    public function getParameterTypeByClass(string $class): ParameterTypeInterface;

    /**
     * Returns all parameter types.
     *
     * @return \Netgen\Layouts\Parameters\ParameterTypeInterface[]
     */
    public function getParameterTypes(): array;
}
