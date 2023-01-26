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
     * Returns the object path. Take note that this is not a slug,
     * but a full path, i.e. starting with /.
     *
     * If the path cannot be generated, this can return null.
     *
     * @deprecated Will be removed in 2.0. Implement
     * ExtendedValueUrlGeneratorInterface and use generateDefaultUrl method
     * instead.
     *
     * @param T $object
     */
    public function generate(object $object): ?string;
}
