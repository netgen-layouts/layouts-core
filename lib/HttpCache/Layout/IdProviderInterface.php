<?php

declare(strict_types=1);

namespace Netgen\Layouts\HttpCache\Layout;

/**
 * ID provider is used to extract all related layout UUIDs for the provided UUID.
 */
interface IdProviderInterface
{
    /**
     * Extracts all relevant UUIDs for a given layout.
     *
     * @param string $layoutId
     *
     * @return string[]
     */
    public function provideIds($layoutId): array;
}
