<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Context;

use ArrayAccess;
use Countable;
use IteratorAggregate;

interface ContextInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Sets a variable to the context. Variable value needs to be
     * a scalar or an array/hash of scalars.
     *
     * @param string $variableName
     * @param mixed $value
     */
    public function set(string $variableName, $value): void;

    /**
     * Adds the provided hash array of values to the context.
     *
     * This replaces already existing variables.
     */
    public function add(array $context): void;

    /**
     * Returns if the variable with provided name exists in the context.
     */
    public function has(string $variableName): bool;

    /**
     * Returns the variable with provided name from the context.
     *
     * @throws \Netgen\BlockManager\Exception\Context\ContextException If variable with provided name does not exist
     *
     * @return mixed
     */
    public function get(string $variableName);

    /**
     * Returns all variables from the context.
     */
    public function all(): array;
}
