<?php

namespace Netgen\BlockManager\Item;

interface ItemBuilderInterface
{
    /**
     * Builds the item from provided object.
     *
     * @param mixed $object
     *
     * @throws \RuntimeException If value cannot be built
     *
     * @return \Netgen\BlockManager\Item\Item
     */
    public function buildFromObject($object);

    /**
     * Builds the item from provided value ID and value type.
     *
     * @param int|string $valueId
     * @param string $valueType
     *
     * @throws \RuntimeException If value cannot be built
     *
     * @return \Netgen\BlockManager\Item\Item
     */
    public function build($valueId, $valueType);
}
