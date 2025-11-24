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
    public function __construct(
        private(set) int $priority = 0,
    ) {}
}
