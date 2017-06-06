<?php

namespace Netgen\BlockManager\HttpCache\Layout;

interface IdProviderInterface
{
    /**
     * Extracts all relevant IDs for a given layout.
     *
     * @param int|string
     * @param mixed $layoutId
     *
     * @return int[]|string[]
     */
    public function provideIds($layoutId);
}
