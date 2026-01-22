<?php

declare(strict_types=1);

namespace Netgen\Layouts\Attribute;

use Attribute;

/**
 * Service tag to autoconfigure target types.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class AsTargetType
{
    public function __construct(
        public private(set) int $priority = 0,
    ) {}
}
