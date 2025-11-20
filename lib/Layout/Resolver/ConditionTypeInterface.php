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
     */
    public function matches(Request $request, mixed $value): bool;

    /*
     * Returns the condition value converted to a format suitable for exporting.
     *
     * This is useful if exported value is different from a stored value, for example
     * when exporting IDs from an external CMS which need to be exported not as IDs
     * but as remote IDs.
     */
    public function export(mixed $value): mixed;

    /*
     * Returns the condition value converted from the exported format.
     *
     * This is useful if stored value is different from an exported value, for example
     * when importing IDs from an external CMS which need to be imported as database IDs
     * in contrast to some kind of remote ID which would be stored in the export.
     */
    public function import(mixed $value): mixed;
}
