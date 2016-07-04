<?php

namespace Netgen\BlockManager\Layout\Resolver;

interface ConditionTypeInterface
{
    /**
     * Returns the condition type.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns the constraints that will be used to validate the condition value.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints();

    /**
     * Returns if this condition matches the provided value.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function matches($value);
}
