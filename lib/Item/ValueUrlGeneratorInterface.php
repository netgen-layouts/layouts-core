<?php

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
     * @param mixed $object
     *
     * @return string
     */
    public function generate($object);
}
