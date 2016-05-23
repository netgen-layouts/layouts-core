<?php

namespace Netgen\BlockManager\Value;

interface ValueLoaderInterface
{
    /**
     * Returns the value type this loader loads.
     *
     * @return string
     */
    public function getValueType();

    /**
     * Loads the value from provided ID.
     *
     * @param int|string $id
     *
     * @throws \RuntimeException If value cannot be loaded
     *
     * @return mixed
     */
    public function load($id);
}
