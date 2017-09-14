<?php

namespace Netgen\BlockManager\Item;

/**
 * Value URL builder generates the URL/path for the provided CMS object.
 * It is used and injected into UrlBuilderInterface which is a central
 * point for generating URLs for items.
 */
interface ValueUrlBuilderInterface
{
    /**
     * Returns the object URL. Take note that this is not a slug,
     * but a full path, i.e. starting with /.
     *
     * @param mixed $object
     *
     * @return string
     */
    public function getUrl($object);
}
