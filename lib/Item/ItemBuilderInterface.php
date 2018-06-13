<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Item;

/**
 * Item builder is a service used as a central point for building items
 * from provided CMS values.
 */
interface ItemBuilderInterface
{
    /**
     * Builds the item from provided object.
     *
     * @param mixed $object
     *
     * @throws \Netgen\BlockManager\Exception\Item\ValueException if value converter does not exist
     *
     * @return \Netgen\BlockManager\Item\ItemInterface
     */
    public function build($object);
}
