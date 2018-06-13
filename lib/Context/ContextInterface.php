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
    public function set($variableName, $value);

    /**
     * Adds the provided hash array of values to the context.
     *
     * This replaces already existing variables.
     *
     * @param array $context
     */
    public function add(array $context);

    /**
     * Returns if the variable with provided name exists in the context.
     *
     * @param string $variableName
     *
     * @return bool
     */
    public function has($variableName);

    /**
     * Returns the variable with provided name from the context.
     *
     * @param string $variableName
     *
     * @throws \Netgen\BlockManager\Exception\Context\ContextException If variable with provided name does not exist
     *
     * @return mixed
     */
    public function get($variableName);

    /**
     * Returns all variables from the context.
     *
     * @return array
     */
    public function all();
}
