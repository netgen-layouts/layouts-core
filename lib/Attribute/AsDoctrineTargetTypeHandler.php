<?php

declare(strict_types=1);

namespace Netgen\Layouts\Attribute;

use Attribute;

/**
 * Service tag to autoconfigure Doctrine target type handlers.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class AsDoctrineTargetTypeHandler
{
    public function __construct(
        public private(set) string $targetType,
    ) {}
}
