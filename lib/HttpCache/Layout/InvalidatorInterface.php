<?php

namespace Netgen\BlockManager\HttpCache\Layout;

interface InvalidatorInterface
{
    /**
     * Invalidates all provided layouts.
     *
     * @param int[]|string[] $layoutIds
     */
    public function invalidate(array $layoutIds);

    /**
     * Invalidates all layouts.
     */
    public function invalidateAll();
}
