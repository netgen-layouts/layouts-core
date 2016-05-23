<?php

namespace Netgen\BlockManager\Value;

interface ValueBuilderInterface
{
    /**
     * Builds the value from provided object.
     *
     * @param mixed $object
     *
     * @throws \RuntimeException If value cannot be built
     *
     * @return \Netgen\BlockManager\Value\Value
     */
    public function buildFromObject($object);

    /**
     * Builds the value from provided item.
     *
     * @param int|string $valueId
     * @param string $valueType
     *
     * @throws \RuntimeException If value cannot be built
     *
     * @return \Netgen\BlockManager\Value\Value
     */
    public function build($valueId, $valueType);
}
