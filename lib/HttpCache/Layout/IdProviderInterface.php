<?php

declare(strict_types=1);

namespace Netgen\BlockManager\HttpCache\Layout;

/**
 * ID provider is used to extract all related layout IDs for the provided ID.
 */
interface IdProviderInterface
{
    /**
     * Extracts all relevant IDs for a given layout.
     *
     * @param int|string $layoutId
     *
     * @return int[]|string[]
     */
    public function provideIds($layoutId);
}
