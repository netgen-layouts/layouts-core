<?php

namespace Netgen\BlockManager\Item;

interface ValueUrlBuilderInterface
{
    /**
     * Returns the value type for which this URL builder builds the URL.
     *
     * @return string
     */
    public function getValueType();

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
