<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver;

use Symfony\Component\HttpFoundation\Request;

/**
 * @method mixed export(mixed $value)
 * @method mixed import(mixed $value)
 *
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

    /*
     * Returns the condition value converted to a format suitable for exporting.
     *
     * This is useful if exported value is different from a stored value, for example
     * when exporting IDs from an external CMS which need to be exported not as IDs
     * but as remote IDs.
     *
     * Will be added to the interface in 2.0.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    // public function export($value);

    /*
     * Returns the condition value converted from the exported format.
     *
     * This is useful if stored value is different from an exported value, for example
     * when importing IDs from an external CMS which need to be imported as database IDs
     * in contrast to some kind of remote ID which would be stored in the export.
     *
     * Will be added to the interface in 2.0.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    // public function import($value);
}
