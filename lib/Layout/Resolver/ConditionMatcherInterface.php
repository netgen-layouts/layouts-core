<?php

namespace Netgen\BlockManager\Layout\Resolver;

interface ConditionMatcherInterface
{
    /**
     * Returns the unique identifier of the condition this matcher matches.
     *
     * @return string
     */
    public function getConditionIdentifier();

    /**
     * Returns if this condition matches the provided value.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function matches($value);
}
