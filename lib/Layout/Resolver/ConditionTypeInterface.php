<?php

namespace Netgen\BlockManager\Layout\Resolver;

interface ConditionTypeInterface
{
    /**
     * Returns the condition type identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns if this condition matches the provided value.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function matches($value);
}
