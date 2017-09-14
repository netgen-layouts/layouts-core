<?php

namespace Netgen\BlockManager\Layout\Resolver;

use Symfony\Component\HttpFoundation\Request;

/**
 * Target type is a high level representation of an URL or a set of URLs
 * as used by the backend CMS on which the layout resolving process is performed.
 *
 * Implementations of this interface provide the constraints used to validate
 * the target values when saving the targets to the database as well as extracting
 * the value from the provided request which will be used to filter valid targets
 * when resolving the layout.
 */
interface TargetTypeInterface
{
    /**
     * Returns the target type.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns the constraints that will be used to validate the target value.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints();

    /**
     * Provides the value for the target to be used in matching process.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return mixed
     */
    public function provideValue(Request $request);
}
