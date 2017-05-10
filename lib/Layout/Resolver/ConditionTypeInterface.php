<?php

namespace Netgen\BlockManager\Layout\Resolver;

use Symfony\Component\HttpFoundation\Request;

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
     * Returns if this request matches the provided value.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param mixed $value
     *
     * @return bool
     */
    public function matches(Request $request, $value);
}
