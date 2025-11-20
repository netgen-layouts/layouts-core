<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item;

/**
 * Instances of this interface generate the path for the provided CMS object.
 * It is used and injected into UrlGeneratorInterface which is a central
 * point for generating URLs for items.
 *
 * @template T of object
 */
interface ValueUrlGeneratorInterface
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
