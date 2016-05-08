<?php

namespace Netgen\BlockManager\Collection;

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
     * @return mixed
     */
    public function load($id);
}
