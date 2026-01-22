<?php

declare(strict_types=1);

namespace Netgen\Layouts\Attribute;

use Attribute;

/**
 * Service tag to autoconfigure condition type form mappers.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class AsConditionTypeFormMapper
{
    public function __construct(
        public private(set) string $conditionType,
    ) {}
}
