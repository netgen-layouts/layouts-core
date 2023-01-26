<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item;

/**
 * Deprecated, will be removed in 2.0 and methods will be moved to
 * ValueUrlGeneratorInterface.
 *
 * @template T of object
 *
 * @extends \Netgen\Layouts\Item\ValueUrlGeneratorInterface<T>
 */
interface ExtendedValueUrlGeneratorInterface extends ValueUrlGeneratorInterface
{
    /**
     * Returns the default object path. Take note that this is not a slug,
     * but a full path, i.e. starting with /.
     *
     * If the path cannot be generated, this can return null.
     *
     * @param T $object
     */
    public function generateDefaultUrl(object $object): ?string;

    /**
     * Returns the admin object path. Take note that this is not a slug,
     * but a full path, i.e. starting with /.
     *
     * If the path cannot be generated, this can return null.
     *
     * @param T $object
     */
    public function generateAdminUrl(object $object): ?string;
}
