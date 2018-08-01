<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Item;

/**
 * Instances of this interface generate the path for the provided CMS object.
 * It is used and injected into UrlGeneratorInterface which is a central
 * point for generating URLs for items.
 */
interface ValueUrlGeneratorInterface
{
    /**
     * Returns the object path. Take note that this is not a slug,
     * but a full path, i.e. starting with /.
     *
     * If the path cannot be generated, this can return null.
     *
     * @param object $object
     *
     * @return string|null
     */
    public function generate($object);
}
