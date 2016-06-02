<?php

namespace Netgen\BlockManager\Layout\Resolver;

interface ConditionMatcherInterface
{
    /**
     * Returns if this condition matches the provided value.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function matches($value);
}
