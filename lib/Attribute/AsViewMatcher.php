<?php

declare(strict_types=1);

namespace Netgen\Layouts\Attribute;

use Attribute;

/**
 * Service tag to autoconfigure view matchers.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class AsViewMatcher
{
    public function __construct(
        public private(set) string $identifier,
    ) {}
}
