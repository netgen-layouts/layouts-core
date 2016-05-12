<?php

namespace Netgen\BlockManager\Collection;

use Netgen\BlockManager\API\Exception\NotFoundException;

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
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If value cannot be loaded
     *
     * @return mixed
     */
    public function load($id);
}
