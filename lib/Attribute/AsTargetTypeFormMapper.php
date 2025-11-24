<?php

declare(strict_types=1);

namespace Netgen\Layouts\Attribute;

use Attribute;

/**
 * Service tag to autoconfigure target type form mappers.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class AsTargetTypeFormMapper
{
    public function __construct(
        private(set) string $type,
    ) {}
}
