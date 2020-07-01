<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver;

use Symfony\Component\HttpFoundation\Request;

/**
 * @method mixed export(mixed $value)
 * @method mixed import(mixed $value)
 *
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
     * Returns the target type identifier.
     */
    public static function getType(): string;

    /**
     * Returns the constraints that will be used to validate the value of
     * the target when storing it to the database.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints(): array;

    /**
     * Provides the value for the target to be used in matching process.
     *
     * This is usually a value extracted from a request. The value should
     * be something that uniquely identifies a page in the CMS or a set of
     * pages.
     *
     * @return mixed
     */
    public function provideValue(Request $request);

    /*
     * Returns the target value converted to a format suitable for exporting.
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
     * Returns the target value converted from the exported format.
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
