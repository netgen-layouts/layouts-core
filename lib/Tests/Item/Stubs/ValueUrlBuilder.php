<?php

namespace Netgen\BlockManager\Tests\Item\Stubs;

use Netgen\BlockManager\Item\ValueUrlBuilderInterface;

class ValueUrlBuilder implements ValueUrlBuilderInterface
{
    /**
     * Returns the value type for which this URL builder builds the URL.
     *
     * @return string
     */
    public function getValueType()
    {
        return 'value';
    }

    /**
     * Returns the object URL. Take note that this is not a slug,
     * but a full path, i.e. starting with /.
     *
     * @param mixed $object
     *
     * @return string
     */
    public function getUrl($object)
    {
        return '/item-url';
    }
}
