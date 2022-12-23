<?php

declare(strict_types=1);

namespace Netgen\Layouts\Attribute;

use Attribute;

/**
 * Service tag to autoconfigure block definition handler plugins.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class AsBlockPlugin
{
    public int $priority;

    public function __construct(int $priority = 0)
    {
        $this->priority = $priority;
    }
}
