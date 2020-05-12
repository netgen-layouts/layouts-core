<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver;

use Symfony\Component\HttpFoundation\Request;

/**
 * Condition type is a high-level model of condition specifications which
 * need to match in order for rule and its layout to be used by the layout
 * resolving process.
 *
 * Implementations of this interface provide constraints used when storing
 * the conditions to the database as well as match the a condition value to
 * provided requests.
 */
interface ConditionTypeInterface
{
    /**
     * Returns the condition type identifier.
     */
    public static function getType(): string;

    /**
     * Returns the constraints that will be used to validate the condition value.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints(): array;

    /**
     * Returns if this request matches the provided value.
     *
     * @param mixed $value
     */
    public function matches(Request $request, $value): bool;
}
